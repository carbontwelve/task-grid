<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Task
 */
class TaskResource extends JsonResource
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
            'type' => 'task',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'authored_by' => $this->authored_by
            ],
            'relationships' => [
                'author' => $this->whenLoaded('author'),
                'worksheet' => $this->whenLoaded('worksheet'),
                'milestones' => MilestoneResource::collection($this->whenLoaded('milestones'))
            ],
            'links' => [
                'self' => action('TaskController@show', ['task' => $this->id]),
            ],
        ];
    }
}
