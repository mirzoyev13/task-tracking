<?php

namespace App\Http\Controllers;

use App\Exceptions\TaskNotFoundException;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('due_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        if ($request->has('status')) { // можно было использовать как отдельную таблицу statuses, так и просто string
            $query->where('status', $request->status);
        }

        $tasks = $query->paginate(10);

        return TaskResource::collection($tasks);
    }


    public function store(Request $request) // можно было использовать TaskRequest, но тут примитивный код
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string|max:50',
            'due_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $task = Task::create($request->all());
        return new TaskResource($task);
    }

    public function show($id)
    {
        $task = Task::find($id);
        if (!$task) {
            throw new TaskNotFoundException();
        }
        return new TaskResource($task);
    }



    public function update(Request $request, $id)
    {
        $task = Task::find($id);
        if (!$task) {
            throw new TaskNotFoundException();
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'status' => 'sometimes|required|string|max:50',
            'due_date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $task->update($request->all());
        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $task = Task::find($id);
        if (!$task) {
            throw new TaskNotFoundException();
        }
        $task->delete();
        return response()->json(null, 204);
    }
}
