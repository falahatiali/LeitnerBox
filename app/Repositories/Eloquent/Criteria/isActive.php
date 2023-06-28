<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class isActive implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->where('active', true);
    }
}
