<?php

namespace App\Http\Resources;

use App\Models\Workbook;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Workbook
 */
class WorkbookResource extends JsonResource
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
            'type' => 'workbook',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'authored_by' => $this->authored_by
            ],
            'relationships' => [
                'author' => $this->whenLoaded('author'),
                'worksheets' => WorksheetResource::collection($this->whenLoaded('worksheets'))
            ],
            'links' => [
                'self' => action('\App\Http\Controllers\WorkbookController@show', ['workbook' => $this->id]),
            ],
        ];
    }
}
