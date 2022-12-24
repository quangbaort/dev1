<?php

namespace App\Services\FileSystem\Contracts;

interface FileBagInterface
{
    /**
     * @return \App\Services\FileSystem\UploadedFileBag
     */
    public function getFileBag();
}
