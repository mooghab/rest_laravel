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

        // inja check mikonim ke todo male hamin user bashe
        if ($todo->user_id != auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'id' => $todo->id,
            'title' => $todo->title,
            'description' => $todo->description,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        if ($todo->user_id != auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $todo->update([
            'title' => $validated['title'] ?? $todo->title,
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'id' => $todo->id,
            'title' => $todo->title,
            'description' => $todo->description,
        ], 200);
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        if ($todo->user_id != auth()->id()) {
            // agar dastresi nadasht 403 bede
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $todo->delete();

        return response()->noContent();
    }
}
