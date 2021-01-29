<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\Worksheet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function index(Worksheet $worksheet): AnonymousResourceCollection
    {
        return TaskResource::collection($worksheet->tasks()->get());
    }

    public function store(Worksheet $worksheet, TaskRequest $request): TaskResource
    {
        return new TaskResource($request->persist($worksheet));
    }

    public function show(Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    public function update(TaskRequest $request): TaskResource
    {
        return new TaskResource($request->persist());
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function restore(int $id): JsonResponse
    {
        Task::withTrashed()->findOrFail($id)->restore();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
