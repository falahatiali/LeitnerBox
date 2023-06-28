<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\DateTimeCriterionInterface;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;

class EndDate implements DateTimeCriterionInterface
{
    private DateTimeInterface $endDate;

    public function apply($entity)
    {
        return $entity->where('created_at', '<=', $this->endDate);
    }

    public function setParams($date)
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        if (!$date instanceof DateTimeInterface) {
            throw new Exception("Class " . get_class($date) . " must be an instance of DateTimeInterface");
        }

        $this->endDate = $date;
    }
}
