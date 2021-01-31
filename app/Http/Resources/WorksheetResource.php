<?php

namespace App\Http\Resources;

use App\Models\Worksheet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Worksheet
 */
class WorksheetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'type' => 'worksheet',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'authored_by' => $this->authored_by
            ],
            'relationships' => [
                'author' => $this->whenLoaded('author'),
                'workbook' => $this->whenLoaded('workbook'),
                'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
                'milestones' => MilestoneResource::collection($this->whenLoaded('milestones'))
            ],
            'links' => [
                'self' => action('\App\Http\Controllers\WorksheetController@show', ['worksheet' => $this->id]),
            ],
        ];
    }
}
