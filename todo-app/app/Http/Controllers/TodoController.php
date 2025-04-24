<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    // Get all todos as JSON (for the frontend)
    public function index()
    {
        // Could add ordering or filtering here later
        return response()->json(Todo::all());
    }

    // Store a new todo item
    public function store(Request $request)
    {
        // Not doing deep validation here, just basic creation
        $todo = Todo::create([
            'title' => $request->input('title'),
            'reminder_at' => $request->input('reminder_at'),
            'completed' => $request->input('completed', false), // default to not completed
            'completed_at' => $request->input('completed') ? now() : null
        ]);
        return response()->json($todo);
    }

    // Update an existing todo
    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id); // will throw 404 if not found
        // Only update fields if provided, otherwise keep old values
        $todo->update([
            'title' => $request->input('title', $todo->title),
            'reminder_at' => $request->input('reminder_at', $todo->reminder_at),
            'completed' => $request->input('completed', $todo->completed),
            // If completed is set, set completed_at to now, if unchecked, set to null, else keep old value
            'completed_at' => $request->input('completed') ? now() : ($request->input('completed') === 0 ? null : $todo->completed_at)
        ]);
        return response()->json($todo);
    }

    // Delete a todo by id
    public function destroy($id)
    {
        // Just delete, no soft delete for now
        Todo::destroy($id);
        return response()->json(['success' => true]);
    }
}
