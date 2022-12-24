<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Traits\Macroable;

class AppHelpers
{
    use Macroable;

    /**
     * Get organization request
     *
     * Default, after login user need select 1 organization.
     * All request from client to server require selected organization
     *
     * @return string
     */
    public function organAccess($field = null)
    {
        $accessed = optional(app(Request::class)->organization);
        if (is_null($field)) {
            return $accessed;
        }

        return $accessed->get($field);
    }

    /**
     * Get only id of organization accessed
     *
     * @return string
     */
    public function organId()
    {
        return $this->organAccess('id');
    }

    /**
     * Get list of prefectures in Japanese
     */
    public function prefectures()
    {
        return config('prefectures');
    }

    /**
     * @param $prefectureCode
     *
     * @return mixed|string
     */
    public function getPrefectureName($prefectureCode)
    {
        $prefectures = $this->prefectures();

        return array_key_exists($prefectureCode, $prefectures) ? $prefectures[$prefectureCode] : '';
    }

    public function getPrefectureCode($prefectureName)
    {
        $prefectures = $this->prefectures();

        return array_search($prefectureName, $prefectures);
    }

    /**
     * Formula of numerical order for paginate
     *
     * @param int $iteration starts at 1 (default of loop)
     * @param int $currentPage starts at 1 (default of paginate)
     * @param int $perPage
     *
     * @return int
     */
    public function numberOrder($iteration, $currentPage, $perPage = PAGE_SIZE)
    {
        return ($currentPage - 1) * $perPage + $iteration;
    }
}
