<?php

namespace App\Repositories;

use App\Models\File;
use App\Repositories\Concerns\BaseRepository;

class FileManageRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return File::class;
    }

    /**
     * @param $organId
     * @param $type
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function folders($organId, $type)
    {
        return $this->model
            ->selectRaw('COUNT(`year`) as total_file, `year`')
            ->type($type)
            ->has($type)
            ->ofOrgan($organId)
            ->groupBy('year')
            ->get();
    }

    /**
     * Check exists file in fileable id
     *
     * @param array|string $fileableIds
     */
    public function fileExists($organId, $fileableIds)
    {
        $fileableIds = !is_array($fileableIds) ? [$fileableIds] : $fileableIds;

        return $this->model->ofOrgan($organId)->whereIn('fileable_id', $fileableIds)->exists();
    }
}
