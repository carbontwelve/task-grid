<?php

namespace App\Http\Controllers;

use App\Http\Requests\MilestoneRequest;
use App\Http\Resources\MilestoneResource;
use App\Models\Milestone;
use App\Models\Worksheet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MilestoneController extends Controller
{
    public function index(Worksheet $worksheet): AnonymousResourceCollection
    {
        return MilestoneResource::collection($worksheet->milestones()->get());
    }

    public function store(MilestoneRequest $request): MilestoneResource
    {
        return new MilestoneResource($request->persist());
    }

    public function show(Milestone $milestone)
    {
        return new MilestoneResource($milestone);
    }

    public function update(MilestoneRequest $request): MilestoneResource
    {
        return $this->store($request);
    }

    public function destroy(Milestone $milestone): JsonResponse
    {
        $milestone->delete();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
