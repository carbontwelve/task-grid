<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\Worksheet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

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

    public function milestone(Task $task, Milestone $milestone, Request $request)
    {
        $this->validate($request, [
            'urgency' => ['required', Rule::in([
                Task::UrgencyShowStopper,
                Task::UrgencyRequired,
                Task::UrgencyNiceToHave,
                Task::UrgencyNotRequired
            ])]
        ]);

        if ($milestone->tasks()->where('task_id', $task->id)->exists()) {
            $milestone->tasks()->updateExistingPivot($task->id,  ['urgency' => $request->input('urgency')]);
        } else {
            $milestone->tasks()->attach($task->id, ['urgency' => $request->input('urgency')]);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
