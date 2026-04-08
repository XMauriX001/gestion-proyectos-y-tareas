<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProyectoRequest;
use App\Models\Proyecto;
use App\Models\EstadoProyecto;
use App\Models\HistorialProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::where('creado_por', Auth::id())
            ->with('estado')
            ->paginate(15);

        return response()->json($proyectos);
    }

    public function show($id)
    {
        $proyecto = Proyecto::with(['estado', 'creador'])->findOrFail($id);

        if ($proyecto->creado_por !== Auth::id() && !Auth::user()->can('ver_proyecto')) {
            return response()->json(['message' => 'No estás autorizado para ver este proyecto'], 403);
        }

        return response()->json($proyecto);
    }

    public function history($id)
    {
        $proyecto = Proyecto::findOrFail($id);

        if ($proyecto->creado_por !== Auth::id() && !Auth::user()->can('ver_historial')) {
            return response()->json(['message' => 'No autorizado para ver el historial'], 403);
        }

        $historial = HistorialProject::where('id_proyecto', $id)
            ->with('usuario')
            ->orderBy('changed_at', 'desc')
            ->paginate(20);

        return response()->json($historial);
    }

    public function store(StoreProyectoRequest $request)
    {
        $estadoActivo = EstadoProyecto::where('estado', 'activo')->first();

        $proyecto = Proyecto::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_final' => $request->fecha_final,
            'creado_por' => Auth::id(),
            'id_estado' => $estadoActivo ? $estadoActivo->id_estado : 1,
        ]);

        HistorialProject::create([
            'id_proyecto' => $proyecto->id_proyecto,
            'id_cambiado_por' => Auth::id(),
            'accion' => 'created',
            'detalles' => json_encode(['estado_inicial' => 'activo']),
            'changed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Proyecto creado de forma exitosa',
            'data' => $proyecto
        ], 201);
    }
}
