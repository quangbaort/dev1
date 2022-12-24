<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\FileManage\DestroyFolderRequest;
use App\Http\Requests\FileManage\MakeDirRequest;
use App\Http\Requests\FileManage\RenameFolderRequest;
use App\Http\Requests\FileManage\UploadFileRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\FileResource;
use App\Services\FileManagementService;
use Illuminate\Http\Request;

class FileManagement extends ApiController
{
    /**
     * @var \App\Services\FileManagementService
     */
    public $fileManagementService;

    /**
     * @param \App\Services\FileManagementService $fileManagementService
     */
    public function __construct(FileManagementService $fileManagementService)
    {
        $this->fileManagementService = $fileManagementService;
    }

    /**
     * Get all files by path
     */
    public function files(Request $request, $type, $folder = null)
    {
        return FileResource::collection($this->fileManagementService->files($request, $type, $folder));
    }

    /**
     * Delete file
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyFile($id)
    {
        $this->fileManagementService->destroyFile($id);

        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @param $nameFolder
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function folders(Request $request, $nameFolder)
    {
        return BaseResource::collection($this->fileManagementService->folders($request, $nameFolder));
    }

    /**
     * @param \App\Http\Requests\FileManage\MakeDirRequest $request
     *
     * @return \App\Http\Resources\BaseResource
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function makeDir(MakeDirRequest $request)
    {
        return new BaseResource($this->fileManagementService->makeDir($request));
    }

    /**
     * @param \App\Http\Requests\FileManage\UploadFileRequest $request
     *
     * @throws \Throwable
     */
    public function upload(UploadFileRequest $request)
    {
        $this->fileManagementService->upload($request);

        $this->responseSuccess(trans('storage.file_uploaded'));
    }

    /**
     * @param \App\Http\Requests\FileManage\RenameFolderRequest $request
     *
     * @return \App\Http\Resources\BaseResource
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function renameFolder(RenameFolderRequest $request)
    {
        return new BaseResource($this->fileManagementService->renameFolder($request));
    }

    /**
     * @param \App\Http\Requests\FileManage\DestroyFolderRequest $request
     * @param $folderId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyFolder(DestroyFolderRequest $request, $folderId)
    {
        $this->fileManagementService->destroyFolder($request, $folderId);

        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * Get storage used and limit of organization
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function storageFree(Request $request)
    {
        $organ   = new \App\Models\Organization($request->organization->toArray());
        $storage = [
            'storage_limit' => $organ->storage_limit,
            'storage_used'  => $organ->storage_used,
        ];

        return new BaseResource($storage);
    }
}
