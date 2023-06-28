<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\DateTimeCriterionInterface;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;

class StartDate implements DateTimeCriterionInterface
{
    protected $startDate;

    public function apply($entity)
    {
        return $entity->where('created_at', '>=', $this->startDate);
    }

    public function setParams($date)
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        if (!$date instanceof DateTimeInterface) {
            throw new Exception("Class " . get_class($date) . " must be an instance of DateTimeInterface");
        }

        $this->startDate = $date;
    }
}
