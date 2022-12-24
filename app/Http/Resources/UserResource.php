<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class UserResource extends JsonResource
{
    /**
     * Response with token
     *
     * @var bool | string
     */
    protected $token = false;

    /**
     * Token expires timestamp when use $token
     *
     * @var bool | int
     */
    protected $expires = false;

    /**
     * Token type
     *
     * @var string
     */
    protected $tokenType = 'bearer';

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $result = parent::toArray($request);

        if (!$this->whenLoaded('companies') instanceof MissingValue) {
            $result['company'] = new CompanyResource($this->resource->company);
            unset($result['companies']);
        }

        if (!$this->whenLoaded('organizations') instanceof MissingValue) {
            $organization = $this->resource->organizations->first();
            $result['organization'] = new OrganizationResource($organization);
            $result['role'] = $organization->pivot->role;
        }

        if ($this->token != false) {
            $result['jwt'] = [
                'access_token' => $this->token,
                'token_type'   => $this->tokenType,
                'expires_in'   => $this->expires,
            ];
        }

        return $result;
    }

    /**
     * Response with token for API
     *
     * @param $token
     * @param $expires
     *
     * @return $this
     */
    public function withToken($token, $expires)
    {
        $this->token   = $token;
        $this->expires = $expires;

        return $this;
    }
}
