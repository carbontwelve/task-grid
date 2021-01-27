<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorksheetRequest;
use App\Http\Resources\WorksheetResource;
use App\Models\Workbook;
use App\Models\Worksheet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class WorksheetController extends Controller
{
    public function index(Workbook $workbook): AnonymousResourceCollection
    {
        return WorksheetResource::collection($workbook->worksheets()->get());
    }

    public function store(WorksheetRequest $request, ?Workbook $workbook = null): WorksheetResource
    {
        return new WorksheetResource($request->persist($workbook));
    }

    public function show(Worksheet $worksheet): WorksheetResource
    {
        return new WorksheetResource($worksheet);
    }

    public function update(WorksheetRequest $request): WorksheetResource
    {
        return $this->store($request);
    }

    public function destroy(Worksheet $worksheet): JsonResponse
    {
        $worksheet->delete();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function restore(int $id)
    {
        Worksheet::withTrashed()->findOrFail($id)->restore();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
