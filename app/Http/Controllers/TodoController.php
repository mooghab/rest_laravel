<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $todos = Todo::where('user_id', auth()->id())->get(['id', 'title', 'description']);

        return response()->json($todos, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $todo = Todo::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'id' => $todo->id,
            'title' => $todo->title,
            'description' => $todo->description,
        ], 201);
    }

    public function show($id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json([
            'id' => $todo->id,
            'title' => $todo->title,
            'description' => $todo->description,
        ], 200);
    }
}
