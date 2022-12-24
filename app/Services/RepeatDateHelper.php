<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class RepeatDateHelper
{
    /**
     * @var \Carbon\Carbon
     */
    protected $startDate;

    /**
     * @var \Carbon\Carbon
     */
    protected $endDate;

    /**
     * List ids of user need mapping
     *
     * @var array
     */
    protected $userMapping;

    /**
     * @var array
     */
    private $periodDates;

    /**
     * @param string | Carbon $startDate
     * @param string | Carbon $endDate
     */
    public function __construct($startDate, $endDate, $userMapping = [])
    {
        $this->startDate = Carbon::parse($startDate);
        $this->endDate   = Carbon::parse($endDate);
        $this->userMapping = $userMapping;
    }

    /**
     * @return array
     */
    public function immediate()
    {
        $this->periodDates = [$this->startDate];

        return $this->resultData();
    }

    /**
     * @return array
     */
    public function daily()
    {
        $this->periodDates = CarbonPeriod::create($this->startDate, $this->endDate);

        return $this->resultData();
    }

    /**
     * @param array $days
     *
     * @return array
     */
    public function dayOfWeek(array $days)
    {
        $period = CarbonPeriod::create($this->startDate, $this->endDate);

        foreach ($period as $date) {
            if (in_array($date->dayOfWeek, $days)) {
                $this->periodDates[] = $date;
            }
        }

        return $this->resultData();
    }

    /**
     * @param $day
     *
     * @return array
     */
    public function dayOfMonth($day)
    {
        $period = CarbonPeriod::create($this->startDate, $this->endDate);

        foreach ($period as $date) {
            if ($date->day == $day) {
                $this->periodDates[] = $date;
            }
        }

        return $this->resultData();
    }

    /**
     * @param array $users
     *
     * @return $this
     */
    public function updateUserMapping($users)
    {
        $this->userMapping = $users;

        return $this;
    }

    /**
     * @return array
     */
    protected function resultData()
    {
        if (empty($this->userMapping)) {
            return $this->periodDates;
        }

        $result = [];
        foreach ($this->periodDates as $date) {
            foreach ($this->userMapping as $userId) {
                $result[] = [
                    'user_id' => $userId,
                    'notified_at' => $date->toDateString()
                ];
            }
        }

        return $result;
    }
}
