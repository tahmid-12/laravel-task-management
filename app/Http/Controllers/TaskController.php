<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;


class TaskController extends Controller
{
    public function getTasks(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is invalid'], 400);
        }

        $tasks = Task::where('user_id', $user->id)->get();
        return response()->json($tasks);
    }

    public function createTask(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is invalid'], 400);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $task = Task::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'status' => $request->status,
        ]);

        return response()->json($task, 201);
    }

    public function showTask(Request $request, $id)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is invalid'], 400);
        }

        $task = Task::where('user_id', $user->id)->findOrFail($id);
        return response()->json($task);
    }

    public function updateTask(Request $request, $id)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is invalid'], 400);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:Low,Medium,High',
            'status' => 'nullable|in:Pending,In Progress,Completed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $task = Task::where('user_id', $user->id)->findOrFail($id);
        $task->update($request->only(['title', 'description', 'due_date', 'priority', 'status']));

        return response()->json($task);
    }

    public function deleteTask(Request $request, $id)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is invalid'], 400);
        }

        $task = Task::where('user_id', $user->id)->findOrFail($id);
        $task->delete();

        return response()->json(null, 204);
    }
}
