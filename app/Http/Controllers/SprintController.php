<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSprintRequest;
use App\Http\Requests\CloseSprintRequest;
use App\Models\Sprint;
use App\Models\Proyecto;
use App\Models\EstadoSprint;
use App\Models\EstadoTarea;
use App\Models\EstadoProyecto;
use App\Models\HistorialProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SprintController extends Controller
{
    public function store(StoreSprintRequest $request, $projectId)
    {
        $proyecto = Proyecto::findOrFail($projectId);

        if ($request->fecha_inicio < $proyecto->fecha_inicio || $request->fecha_final > $proyecto->fecha_final) {
            return response()->json(['message' => 'Las fechas del sprint deben estar dentro de las fechas del proyecto'], 400);
        }

        $estadoActivo = EstadoSprint::where('estado', 'activo')->first();

        $sprint = Sprint::create([
            'id_proyecto' => $proyecto->id_proyecto,
            'id_creado_por' => Auth::id(),
            'id_estado' => $estadoActivo ? $estadoActivo->id_estado : 1,
            'titulo' => $request->titulo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_final' => $request->fecha_final,
        ]);

        HistorialProject::create([
            'id_proyecto' => $proyecto->id_proyecto,
            'id_cambiado_por' => Auth::id(),
            'accion' => 'sprint_created',
            'detalles' => json_encode(['sprint_id' => $sprint->id_sprint, 'titulo' => $sprint->titulo]),
            'changed_at' => now(),
        ]);

        return response()->json(['message' => 'Sprint creado con éxito', 'data' => $sprint], 201);
    }

    public function refreshExpirations(Request $request)
    {
        $estadoCerradoSprint = EstadoSprint::query()->where('estado', 'cerrado')->first();
        if (!$estadoCerradoSprint)
            return response()->json(['message' => 'Configuración faltante'], 500);

        $estadoCompletadaTarea = EstadoTarea::query()->where('estado', 'completada')->first();

        $hoy = Carbon::today();

        $sprintsExpirados = Sprint::query()->where('fecha_final', '<', $hoy)
            ->where('id_estado', '!=', $estadoCerradoSprint->id_estado)
            ->get();

        $conteoSprints = 0;
        $conteoTareas = 0;

        foreach ($sprintsExpirados as $sprint) {
            /** @var \App\Models\Sprint $sprint */
            $sprint->update(['id_estado' => $estadoCerradoSprint->id_estado]);
            $conteoSprints++;

            HistorialProject::create([
                'id_proyecto' => $sprint->id_proyecto,
                'id_cambiado_por' => Auth::id() ?? 1,
                'accion' => 'sprint_closed_auto',
                'detalles' => json_encode(['sprint_id' => $sprint->id_sprint, 'motivo' => 'Cierre en refresh (fecha expirada)']),
                'changed_at' => now(),
            ]);

            if ($estadoCompletadaTarea) {
                $tareasIncompletas = $sprint->tareas()->where('id_estado', '!=', $estadoCompletadaTarea->id_estado)->get();

                if ($tareasIncompletas->isNotEmpty()) {
                    $estadoActivoSprint = EstadoSprint::query()->where('estado', 'activo')->first();
                    $proximoSprint = null;

                    if ($estadoActivoSprint) {
                        $proximoSprint = Sprint::query()->where('id_proyecto', $sprint->id_proyecto)
                            ->where('id_estado', $estadoActivoSprint->id_estado)
                            ->where('fecha_inicio', '>=', $hoy)
                            ->orderBy('fecha_inicio', 'asc')
                            ->first();
                    }

                    $proximoSprintId = $proximoSprint ? $proximoSprint->id_sprint : null;

                    foreach ($tareasIncompletas as $tarea) {
                        $tarea->update(['id_sprint' => $proximoSprintId]);
                        $conteoTareas++;
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Refresh completado',
            'sprints_cerrados' => $conteoSprints,
            'tareas_movidas' => $conteoTareas
        ]);
    }

    public function closeSprint(CloseSprintRequest $request, $id)
    {
        
        $sprint = Sprint::find($id);

        if (!$sprint) {
           
            $proyecto = Proyecto::find($id);
            if ($proyecto) {
                $estadoActivoSprint = EstadoSprint::where('estado', 'activo')->first();
                $sprint = Sprint::where('id_proyecto', $proyecto->id_proyecto)
                    ->where('id_estado', $estadoActivoSprint ? $estadoActivoSprint->id_estado : 1)
                    ->first();
            }
        }

        if (!$sprint) {
            return response()->json(['message' => 'No se encontró el sprint solicitado o no hay un sprint activo para este proyecto'], 404);
        }

        $proyecto = $sprint->proyecto;
        $estadoCerradoSprint = EstadoSprint::where('estado', 'cerrado')->first();

       
        $totalTareas = $sprint->tareas()->count();
        $tareasCompletadas = $sprint->tareas()
            ->where('id_estado', EstadoTarea::where('estado', 'completada')->first()->id_estado ?? 4)
            ->count();
        $tareasEnRevision = $sprint->tareas()
            ->where('id_estado', EstadoTarea::where('estado', 'en_revision')->first()->id_estado ?? 3)
            ->count();
        $tareasEnProgreso = $sprint->tareas()
            ->where('id_estado', EstadoTarea::where('estado', 'en_progreso')->first()->id_estado ?? 2)
            ->count();
        $tareasPorHacer = $sprint->tareas()
            ->where('id_estado', EstadoTarea::where('estado', 'por_hacer')->first()->id_estado ?? 1)
            ->count();

        $velocidad = $tareasCompletadas;
        $porcentajeCompletado = $totalTareas > 0 ? ($tareasCompletadas / $totalTareas) * 100 : 0;

        $estadoCompletada = EstadoTarea::where('estado', 'completada')->first();
        
        // Mover tareas no completadas al siguiente sprint
        $tareasIncompletas = $sprint->tareas()
            ->where('id_estado', '!=', $estadoCompletada ? $estadoCompletada->id_estado : 4)
            ->get();

        
        $proximoSprint = Sprint::where('id_proyecto', $sprint->id_proyecto)
            ->where('id_sprint', '!=', $sprint->id_sprint)
            ->where('fecha_inicio', '>=', $sprint->fecha_inicio)
            ->where('id_estado', '!=', $estadoCerradoSprint ? $estadoCerradoSprint->id_estado : 2)
            ->orderBy('fecha_inicio', 'asc')
            ->first();

        $tareasMov = 0;
        foreach ($tareasIncompletas as $tarea) {
            if ($proximoSprint) {
                $tarea->update(['id_sprint' => $proximoSprint->id_sprint]);
            } else {
                $tarea->update(['id_sprint' => null]);
            }
            $tareasMov++;
        }

        $sprint->update(['id_estado' => $estadoCerradoSprint ? $estadoCerradoSprint->id_estado : 2]);

        $detalles = [
            'sprint_id' => $sprint->id_sprint,
            'sprint_titulo' => $sprint->titulo,
            'total_tareas' => $totalTareas,
            'completadas' => $tareasCompletadas,
            'en_revision' => $tareasEnRevision,
            'en_progreso' => $tareasEnProgreso,
            'por_hacer' => $tareasPorHacer,
            'velocidad' => $velocidad,
            'porcentaje_completado' => round($porcentajeCompletado, 2),
            'tareas_movidas' => $tareasMov,
            'comentario' => $request->comentario ?? null,
        ];

        HistorialProject::create([
            'id_proyecto' => $proyecto->id_proyecto,
            'id_cambiado_por' => Auth::id(),
            'accion' => 'sprint_closed',
            'detalles' => json_encode($detalles),
            'changed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Sprint cerrado exitosamente',
            'data' => [
                'sprint' => $sprint,
                'metricas' => $detalles,
                'proximo_sprint_id' => $proximoSprint ? $proximoSprint->id_sprint : null
            ]
        ]);
    }

    public function closureSummary($id)
    {
        $proyecto = Proyecto::findOrFail($id);

        if ($proyecto->creado_por !== Auth::id() && !Auth::user()->can('ver_historial')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $cierres = HistorialProject::where('id_proyecto', $id)
            ->where('accion', 'sprint_closed')
            ->orderBy('changed_at', 'desc')
            ->get();

        if ($cierres->isEmpty()) {
            return response()->json(['message' => 'No hay cierres registrados para este proyecto'], 404);
        }

        return response()->json([
            'proyecto' => $proyecto,
            'historial_cierres' => $cierres->map(function($cierre) {
                $detalles = is_string($cierre->detalles) ? json_decode($cierre->detalles) : $cierre->detalles;
                return [
                    'fecha_cierre' => $cierre->changed_at,
                    'cerrado_por' => $cierre->usuario ? $cierre->usuario->name : 'Sistema',
                    'metricas' => $detalles,
                ];
            })
        ]);
    }
}