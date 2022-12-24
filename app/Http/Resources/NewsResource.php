<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class NewsResource extends JsonResource
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

        if (!$requester->isSupperAdmin() && !$this->whenLoaded('users') instanceof MissingValue) {
            $result['can_response'] = $this->resource->users
                ->where('id', $requester->id)
                ->whereNull('pivot.read_at')
                ->count();
        }

        if ($requester->isAdminHigher() && !$this->whenLoaded('users') instanceof MissingValue) {
            $totalUserRead = $this->resource->users->whereNotNull('pivot.read_at')->count();
            $result['statistic'] = [
                'read' => $totalUserRead,
                'unread' => $this->resource->users->count() - $totalUserRead,
            ];
            $result['rate'] = ($totalUserRead == 0) ? 0 : round($totalUserRead / $this->resource->users->count() * 100);
            $result['users'] = UserResource::collection($this->users);
        }

        return $result;
    }
}
