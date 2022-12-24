<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class NewsUserResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $result = $this->resource->attributesToArray();
        if (!$this->whenLoaded('user') instanceof MissingValue) {
            $result = array_merge($result, (new UserResource($this->resource->user))->toArray($request));
        }

        return $result;
    }
}
