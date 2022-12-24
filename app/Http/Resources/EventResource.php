<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use App\Models\User;
use App\Models\Department;
class EventResource extends JsonResource
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
        $resource = parent::toArray($request);
        $resource['created_by'] = isset($resource['created_by']) ? User::find($resource['created_by'])->name : null;
        $resource['updated_by'] = isset($resource['updated_by']) ? User::find($resource['updated_by'])->name : null;
        $resource['department_name'] = Department::find($resource['department_id'])->name;

        $resource['can_answer'] = !($request->user()->isSupperAdmin() || $this->resource->isAnswerTimeEnd())
                                  && $this->resource->invited;

        $requester = $request->user();
        if ($requester->isAdminHigher() && !$this->whenLoaded('users') instanceof MissingValue) {
            $resource['users'] = UserResource::collection($this->resource->users);
        }
        if (is_null($this->resource->invited)) {
            unset($resource['can_answer']);
        }

        return $resource;
    }
}
