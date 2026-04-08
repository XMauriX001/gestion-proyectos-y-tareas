<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTareaRequest;
use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\Sprint;
use App\Models\EstadoSprint;
use App\Models\EstadoProyecto;
use App\Models\EstadoTarea;
use App\Models\HistorialProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TareaController extends Controller
{
    public function index($projectId)
    {
        $proyecto = Proyecto::findOrFail($projectId);

        if ($proyecto->creado_por !== Auth::id() && !Auth::user()->can('ver_tarea')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $tareas = Tarea::where('id_proyecto', $projectId)
            ->with(['estado', 'asignado', 'sprint'])
            ->paginate(15);

        return response()->json($tareas);
    }

    public function myTasks()
    {
        $tareas = Tarea::where('id_asignado_a', Auth::id())
            ->with(['proyecto', 'estado'])
            ->paginate(15);

        return response()->json($tareas);
    }

    public function store(StoreTareaRequest $request, $projectId)
    {
        $proyecto = Proyecto::findOrFail($projectId);

        $estadoCerrado = EstadoProyecto::where('estado', 'cerrado')->first();
        if ($proyecto->id_estado === $estadoCerrado->id_estado) {
            return response()->json(['message' => 'No puedes crear tareas en un proyecto cerrado'], 400);
        }

        if ($request->fecha_entrega < $proyecto->fecha_inicio || $request->fecha_entrega > $proyecto->fecha_final) {
            return response()->json(['message' => 'La fecha de entrega debe estar dentro del proyecto'], 400);
        }

        $estadoIni = EstadoTarea::where('estado', 'por_hacer')->first();

        $tarea = Tarea::create([
            'id_proyecto' => $proyecto->id_proyecto,
            'id_creado_por' => Auth::id(),
            'id_estado' => $estadoIni->id_estado ?? 1,
            'id_prioridad' => $request->id_prioridad ?? 2, // 2 = media
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_entrega' => $request->fecha_entrega,
            'id_sprint' => $request->id_sprint,
        ]);

        HistorialProject::create([
            'id_proyecto' => $proyecto->id_proyecto,
            'id_cambiado_por' => Auth::id(),
            'accion' => 'created',
            'detalles' => json_encode(['tarea_id' => $tarea->id_tareas]),
            'changed_at' => now(),
        ]);

        return response()->json(['message' => 'Tarea creada', 'data' => $tarea], 201);
    }

    public function assign(Request $request, $taskId)
    {
        $request->validate(['member_id' => 'required|exists:users,id']);

        $tarea = Tarea::findOrFail($taskId);
        $proyecto = $tarea->proyecto;

        $estadoCompletada = EstadoTarea::where('estado', 'completada')->first();
        if ($estadoCompletada && $tarea->id_estado === $estadoCompletada->id_estado) {
            return response()->json(['message' => 'No puedes reasignar una tarea ya completada'], 400);
        }

        if ($proyecto->creado_por !== Auth::id() && !Auth::user()->can('asignar_tarea')) {
            return response()->json(['message' => 'No autorizado para asignar tareas'], 403);
        }

        $tarea->update(['id_asignado_a' => $request->member_id]);

        HistorialProject::create([
            'id_proyecto' => $proyecto->id_proyecto,
            'id_cambiado_por' => Auth::id(),
            'accion' => 'assigned',
            'detalles' => json_encode([
                'tarea_id' => $tarea->id_tareas,
                'asignado_a' => $request->member_id
            ]),
            'changed_at' => now(),
        ]);

        return response()->json(['message' => 'Tarea asignada', 'data' => $tarea]);
    }

    public function updateStatus(Request $request, $taskId)
    {
        $request->validate(['status' => 'required|string']);

        $tarea = Tarea::findOrFail($taskId);
        $proyecto = $tarea->proyecto;

        if ($tarea->id_asignado_a !== Auth::id() && !Auth::user()->can('cambiar_estado_tarea')) {
            return response()->json(['message' => 'No autorizado para cambiar estado'], 403);
        }

        $nuevoEstado = EstadoTarea::where('estado', $request->status)->first();

        $estadoActualModel = EstadoTarea::find($tarea->id_estado);
        $estadoActual = $estadoActualModel ? $estadoActualModel->estado : '';

        if (!$nuevoEstado) {
            return response()->json(['message' => 'Estado solicitado no existe'], 400);
        }

        if ($request->status === 'completada' && !Auth::user()->hasAnyRole(['product_owner', 'project_manager'])) {
            return response()->json(['message' => 'Solo el product owner o project manager pueden marcar la tarea como completada'], 403);
        }

        $transicionesPermitidas = [
            'por_hacer' => ['en_progreso'],
            'en_progreso' => ['en_revision', 'por_hacer'],
            'en_revision' => ['completada', 'en_progreso'],
            'completada' => ['en_revision']
        ];

        if (
            array_key_exists($estadoActual, $transicionesPermitidas) &&
            !in_array($request->status, $transicionesPermitidas[$estadoActual])
        ) {
            return response()->json(['message' => "Transición no permitida de $estadoActual a {$request->status}"], 400);
        }

        $tarea->update(['id_estado' => $nuevoEstado->id_estado]);

        HistorialProject::create([
            'id_proyecto' => $proyecto->id_proyecto,
            'id_cambiado_por' => Auth::id(),
            'accion' => 'status_changed',
            'detalles' => json_encode([
                'tarea_id' => $tarea->id_tareas,
                'nuevo_estado' => $request->status
            ]),
            'changed_at' => now(),
        ]);

        if ($request->status === 'completada' && $tarea->id_sprint) {
            $sprint = Sprint::find($tarea->id_sprint);
            if ($sprint) {
                $totalTareas = $sprint->tareas()->count();
                $tareasCompletadas = $sprint->tareas()->where('id_estado', $nuevoEstado->id_estado)->count();

                if ($totalTareas > 0 && $totalTareas === $tareasCompletadas) {
                    $estadoCerradoSprint = EstadoSprint::where('estado', 'cerrado')->first();
                    if ($estadoCerradoSprint && $sprint->id_estado !== $estadoCerradoSprint->id_estado) {
                        $sprint->update(['id_estado' => $estadoCerradoSprint->id_estado]);

                        HistorialProject::create([
                            'id_proyecto' => $sprint->id_proyecto,
                            'id_cambiado_por' => Auth::id(),
                            'accion' => 'sprint_closed',
                            'detalles' => json_encode(['sprint_id' => $sprint->id_sprint, 'motivo' => 'Todas las tareas completadas']),
                            'changed_at' => now(),
                        ]);
                    }
                }
            }
        }

        return response()->json(['message' => 'Estado actualizado', 'data' => $tarea]);
    }
}
