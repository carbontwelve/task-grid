<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkbookRequest;
use App\Http\Resources\WorkbookResource;
use App\Models\User;
use App\Models\Workbook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class WorkbookController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();
        return WorkbookResource::collection($user->workbooks()->get());
    }

    public function show(Workbook $workbook): WorkbookResource
    {
        return new WorkbookResource($workbook);
    }

    public function update(WorkbookRequest $request): WorkbookResource
    {
        return $this->store($request);
    }

    public function store(WorkbookRequest $request): WorkbookResource
    {
        return new WorkbookResource($request->persist());
    }

    public function destroy(Workbook $workbook): JsonResponse
    {
        $workbook->delete();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function restore(int $id): JsonResponse
    {
        Workbook::withTrashed()->findOrFail($id)->restore();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
