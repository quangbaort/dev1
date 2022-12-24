<?php

namespace App\Rules;

use App\Helpers\Facades\FileManager;
use Illuminate\Contracts\Validation\Rule;

class StorageAvailRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_null($value)) {
            return true;
        }

        if (is_array($value)) {
            $totalSize = 0;
            foreach ($value as $file) {
                if (!$this->isValidFile($file)) {
                    break;
                }

                $totalSize += $file->getSize();
            }

            return $totalSize > 0 && FileManager::canUpload($totalSize);
        }

        if (!$this->isValidFile($value)) {
            return false;
        }

        return FileManager::canUpload($value->getSize());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('storage.space_not_enough');
    }

    /**
     * Check that the given file is a valid file instance.
     *
     * @param  mixed  $file
     * @return bool
     */
    protected function isValidFile($file)
    {
        return $file instanceof \SplFileInfo && $file->getPath() !== '';
    }
}
