<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SafetyResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $result = parent::toArray($request);
        $result['notified_at'] = $this->resource->notified_at->toDateString();

        if (!$this->whenLoaded('user') instanceof MissingValue) {
            $result['user'] = new UserResource($this->resource->user);
        }

        return $result;
    }
}
