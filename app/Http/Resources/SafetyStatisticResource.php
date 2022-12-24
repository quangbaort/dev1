<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SafetyStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $result = parent::toArray($request);

        $result['responses'] = SafetyResponseResource::collection($this->whenLoaded('responses'));

        $totalResponses = $this->resource->responses->count();

        $result['no_answer'] = ($totalResponses == 0) ? 0 : ceil($this->resource->responses
                                   ->where('answer', SAFETY_NO_ANSWER)
                                   ->count() / $totalResponses * 100);
        $result['safe'] = ($totalResponses == 0) ? 0 : ceil($this->resource->responses
                              ->where('answer', SAFETY_ANSWER_SAFE)
                              ->count() / $totalResponses * 100);
        $result['unsafe'] = ($totalResponses == 0) ? 0 : ceil($this->resource->responses
                                ->where('answer', SAFETY_ANSWER_NG)
                                ->count() / $totalResponses * 100);
        $result['answered'] = ($totalResponses == 0) ? 0 : ceil($this->resource->responses
                                  ->where('answer', '!=', SAFETY_NO_ANSWER)
                                  ->count() / $totalResponses * 100);

        return $result;
    }
}
