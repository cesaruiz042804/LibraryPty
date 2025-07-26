<?php

namespace App\Http\Controllers\Api;

use App\Models\Libro;
use Illuminate\Http\Request;
use App\Http\Resources\LibroResource;
use App\Http\Controllers\Controller;

class LibroController extends Controller
{
    public function index()
    {
        return LibroResource::collection(Libro::with('category')->get());
    }

    public function show($id)
    {
        $libro = Libro::with('category')->findOrFail($id);
        return new LibroResource($libro);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'sinopsis' => 'nullable|string',
            'imagen' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);
        $libro = Libro::create($validated);
        return new LibroResource($libro);
    }

    public function update(Request $request, $id)
    {
        $libro = Libro::findOrFail($id);
        $validated = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'autor' => 'sometimes|required|string|max:255',
            'sinopsis' => 'nullable|string',
            'imagen' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);
        $libro->update($validated);
        return new LibroResource($libro);
    }

    public function destroy($id)
    {
        $libro = Libro::findOrFail($id);
        $libro->delete();
        return response()->json(['message' => 'Libro eliminado correctamente.']);
    }
} 