<?php

namespace App\Http\Resources;

use App\Models\Milestone;
use App\Models\Worksheet;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Milestone
 */
class MilestoneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'milestone',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'authored_by' => $this->authored_by
            ],
            'relationships' => [
                'author' => $this->whenLoaded('author'),
                'worksheet' => WorksheetResource::collection($this->whenLoaded('worksheet')),
                'tasks' => TaskResource::collection($this->whenLoaded('tasks'))
            ],
            'links' => [
                'self' => action('MilestoneController@show', ['milestone' => $this->id]),
            ],
        ];
    }
}
