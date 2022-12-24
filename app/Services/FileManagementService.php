<?php

namespace App\Services;

use App\Helpers\Facades\FileManager;
use App\Repositories\FileManageRepository;
use App\Repositories\FolderRepository;
use App\Services\Concerns\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileManagementService extends BaseService
{
    /**
     * @var \App\Repositories\FolderRepository
     */
    protected $folderRepository;

    /**
     * @param \App\Repositories\FileManageRepository $fileManageRepository
     * @param \App\Repositories\FolderRepository $folderRepository
     */
    public function __construct(FileManageRepository $fileManageRepository, FolderRepository $folderRepository)
    {
        $this->repository       = $fileManageRepository;
        $this->folderRepository = $folderRepository;
    }

    /**
     * Get list folders of type by year
     *
     * @param \Illuminate\Http\Request $request
     * @param string $type
     *
     * @return array|array[]
     */
    public function folders(Request $request, $type)
    {
        if ($type == FILE_TYPE_FOLDER) {
            return $this->generalFolders($request);
        }

        return $this->repository->folders($request->organization->get('id'), $type)
            ->map(function ($item) use ($type) {
                $item['name'] = trans(
                    'app.folder_name_with_count_files',
                    ['name' => $item->year, 'count' => $item->total_file]
                );
                $item['path'] = $item->year;
                $item['type'] = $type;

                return $item;
            });
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function generalFolders(Request $request)
    {
        return $this->folderRepository
            ->getModel()
            ->ofOrgan($request->organization->get('id'))
            ->whereNotNull('parent_id')
            ->get()
            ->toTree();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $type
     * @param $folder
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function files(Request $request, $type, $folder = null)
    {
        $this->makeBuilder();
        $this->builder->type($type)->ofOrgan($request->organization->get('id'));

        if ($type == FILE_TYPE_FOLDER) {
            $this->builder->with('creator');
        }

        // When searching, remove filter by folder. Filter all files
        if (!is_null($folder) && !$this->filter->has('keyword') && !$this->filter->has('title')) {
            $this->builder->inFolder($folder, $type);
        }

        //Make join with morph relationship
        $relationTableName = Str::pluralStudly(($type == FILE_TYPE_MEETING) ? FILE_TYPE_EVENT : $type);
        $this->builder->joinRelationship($type);

        //Add select relation columns
        switch ($type) {
            case FILE_TYPE_EVENT:
            case FILE_TYPE_MEETING:
                $selectColumns = ['title', 'start'];
                break;
            case FILE_TYPE_NEWS:
                $selectColumns = ['title', 'updated_at'];
                break;
            default:
                $selectColumns = ['name', 'updated_at'];
        }
        $addSelect = [];

        foreach ($selectColumns as $column) {
            $addSelect[] = $relationTableName . '.' . $column . ' as ' . $type . '_' . $column;
        }
        $this->builder->addSelect($addSelect);

        /**
         * Filters
         */
        if ($this->filter->has('keyword')) {
            $this->builder->where($relationTableName . '.title', 'LIKE', '%' . $this->filter->get('keyword') . '%');
        }

        if ($this->filter->has('title')) {
            $this->builder->where('files.title', 'LIKE', '%' . $this->filter->get('title') . '%');
        }

        /**
         * Order by
         */
        $sortColumn = '';
        $sortType = 'DESC';
        $sortRequest = $this->filter->get('sort');
        if (!empty($sortRequest)) {
            $sortColumn = array_key_first($sortRequest);
            $sortType = $sortRequest[$sortColumn];
        }

        $sortColumn = match ($type) {
            FILE_TYPE_EVENT, FILE_TYPE_MEETING =>
                empty($sortColumn) || $sortColumn == 'start' ? $type . '_start' : $sortColumn,
            FILE_TYPE_NEWS => empty($sortColumn) || $sortColumn == 'updated_at' ? $type . '_updated_at' : $sortColumn,
            FILE_TYPE_FOLDER => empty($sortColumn) ? $type . '_updated_at' : $sortColumn
        };


        $this->builder->orderBy($sortColumn, $sortType);

        return $this->builder->paginate($this->filter->get('limit'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function makeDir(Request $request)
    {
        $organId = $request->organization->get('id');

        $requestData                    = $request->toArray();
        $requestData['organization_id'] = $organId;

        if (is_null($request->input('parent_id'))) {
            $root = $this->folderRepository->rootOf($organId);
            if (is_null($root)) {
                $root = $this->folderRepository->makeRootDir($organId, $request->organization->get('name'));
            }
        } else {
            $root = $this->folderRepository->find($request->input('parent_id'));
        }

        $requestData['parent_id'] = $root->id;

        return $this->folderRepository->create($requestData);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     * @throws \Throwable
     */
    public function upload(Request $request)
    {
        $folder = $this->folderRepository->find($request->input('path'));

        FileManager::ofModel($folder)->upload($request->file);

        return true;
    }

    /**
     * Delete file by id
     * @param $id
     */
    public function destroyFile($id)
    {
        $file = $this->repository->find($id);
        FileManager::delete($file);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function renameFolder(Request $request)
    {
        return $this->folderRepository->updateByModel(
            $this->folderRepository->find($request->input('path')),
            $request->only('name')
        );
    }

    /**
     * delete folder and sub folders
     *
     * @param \Illuminate\Http\Request $request
     * @param string $folderId
     *
     * @return bool
     */
    public function destroyFolder(Request $request, $folderId)
    {
        $organId = $request->organization->get('id');

        // Get all descendants of folder
        $descendants = $this->folderRepository->descendantsAndSelf($organId, $folderId);
        abort_if($descendants->isEmpty(), 404, trans('storage.dir_not_exist'));

        // Check file exists
        abort_if(
            $this->repository->fileExists($organId, $descendants->pluck('id')->toArray()),
            406,
            trans('storage.delete_folder_failed')
        );

        $this->folderRepository
            ->deleteWhere([
                ['id', 'IN', $descendants->pluck('id')->toArray()]
              ]);

        return true;
    }
}
