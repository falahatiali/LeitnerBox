<?php

namespace App\Repositories\Criteria;

interface DateTimeCriterionInterface extends CriterionInterface
{
    public function setParams($date);
}
