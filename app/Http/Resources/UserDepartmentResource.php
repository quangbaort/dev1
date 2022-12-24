<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($result)
    {
        return [
            'organization' => $this->org_name,
            'department' => $this->dep_name,
            'name' => $this->user_name,
            'company' => $this->comp_name,
            'prefecture' => $this->com_prefecture,
            'city' => $this->com_city
        ];
    }
}
