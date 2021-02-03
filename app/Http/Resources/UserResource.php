<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
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
            'type' => 'user',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'joined' => $this->created_at,
                'avatar' => "https://www.gravatar.com/avatar/" . md5(strtolower(trim($this->email))) . "?s=85"
            ],
            'relationships' => [],
            'links' => [
                'self' => route('user', ['task' => $this->id]),
            ],
        ];
    }
}
