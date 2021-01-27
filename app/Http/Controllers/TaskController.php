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

    public function store(TaskRequest $request)
    {
        return new TaskResource($request->persist());
    }

    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    public function update(TaskRequest $request)
    {
        return $this->store($request);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
