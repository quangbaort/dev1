<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $result = parent::toArray($request);
        $result['users'] = UserResource::collection($this->whenLoaded('users'));
        $result['can_answer'] = !($request->user()->isSupperAdmin() || $this->resource->isAnswerTimeEnd())
                                  && $this->resource->invited;

        if (is_null($this->resource->invited)) {
            unset($result['can_answer']);
        }

        return $result;
    }
}
