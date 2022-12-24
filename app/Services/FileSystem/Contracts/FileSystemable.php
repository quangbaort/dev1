<?php

namespace App\Services\FileSystem\Contracts;

interface FileSystemable
{
    /**
     * Get the class name of the parent model.
     *
     * @return string
     */
    public function getMorphClass();
}
