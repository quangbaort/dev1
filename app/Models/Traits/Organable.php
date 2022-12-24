<?php

namespace App\Models\Traits;

use App\Models\Organization;

trait Organable
{
    /**
     * Records only by organization
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $organId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfOrgan($builder, $organId)
    {
        return $builder->where($this->table . '.organization_id', $organId);
    }

    /**
     * Organization Data by relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function organization()
    {
        return $this->hasOne(Organization::class, 'id', 'organization_id');
    }
}
