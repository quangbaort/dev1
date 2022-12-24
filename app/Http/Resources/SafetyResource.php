<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SafetyResource extends JsonResource
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
        $requester = $request->user();
        if ($requester->isAdminHigher() && !$this->whenLoaded('users') instanceof MissingValue) {
            $result['users'] = UserResource::collection($this->resource->users);
        }

        if ($this->whenLoaded('responses') instanceof MissingValue) {
            return $result;
        }

        $today = now()->toDateString();
        $todayResponse = $this->resource->responses->filter(function ($item) use ($today) {
            return $item->notified_at->toDateString() == $today;
        });

        $ofUser = $todayResponse->where('user_id', $requester->id)->first();
        $result['can_response'] = !is_null($ofUser);

        $result['notify_at'] = now()->format('Y/m/d');
        if ($todayResponse->isEmpty()) {
            $lastResponse = $this->resource->responses->sortByDesc('notified_at')->first();
            $result['notify_at'] = is_null($lastResponse) ? '' : $lastResponse->notified_at->format('Y/m/d');
        }

        if ($requester->isAdminHigher()) {
            $result['emergency'] = !$todayResponse
                ->where('answer', SAFETY_ANSWER_NG)
                ->where('response', '!=', SAFETY_SUPPORTED)
                ->isEmpty();

            $totalAnswer = $todayResponse->where('answer', '!=', SAFETY_NO_ANSWER)->count();
            $result['answer_rate'] = ($totalAnswer > 0) ? round($totalAnswer / $todayResponse->count() * 100) : 0;
        } else {
            $result['response'] = is_null($ofUser) ? [] : new SafetyResponseResource($ofUser);
        }

        unset($result['responses']);

        return $result;
    }
}
