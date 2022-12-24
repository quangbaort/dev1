<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Concerns\BaseRepository;

class UserRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * @param $ids
     *
     * @return mixed
     */
    public function getMailOfUser($ids)
    {
        return User::whereIn('id', $ids)->pluck('email', 'id');
    }

    /**
     * @param string $email
     *
     * @return User | null
     */
    public function byEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Detail user with all relationships
     */
    public function detail($userId, $organId)
    {
        return $this->model
            ->withCompany($organId)
            ->withDepartment($organId)
            ->withOrganization($organId)
            ->findOrFail($userId);
    }
}
