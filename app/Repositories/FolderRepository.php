<?php

namespace App\Repositories;

use App\Models\Folder;
use App\Repositories\Concerns\BaseRepository;

class FolderRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Folder::class;
    }

    /**
     * @param $organId
     *
     * @return mixed
     */
    public function rootOf($organId)
    {
        return $this->model->where('organization_id', $organId)->whereNull('parent_id')->first();
    }

    /**
     * @param string $organId
     * @param string $name
     *
     * @return mixed
     */
    public function makeRootDir($organId, $name = null)
    {
        return $this->model->create(['organization_id' => $organId, 'parent_id' => null, 'name' => 'Root ' . $name]);
    }

    /**
     *
     */
    public function descendantsAndSelf($organId, $ofId)
    {
        return $this->model->descendantsAndSelf($ofId);
    }
}
