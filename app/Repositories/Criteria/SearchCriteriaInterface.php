<?php

namespace App\Repositories\Criteria;

interface SearchCriteriaInterface extends CriterionInterface
{
    public function setParams(string $search);
}
