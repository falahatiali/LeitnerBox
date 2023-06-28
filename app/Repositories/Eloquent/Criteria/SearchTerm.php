<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\SearchCriteriaInterface;
use Illuminate\Support\Facades\Schema;

class SearchTerm implements SearchCriteriaInterface
{
    protected string $search;

    public function apply($entity)
    {
        $query = $entity->where('name', 'LIKE', "%{$this->search}%");

        if (Schema::hasColumn($entity->getModel()->getTable(), 'description')) {
            $query = $query->orWhere('description', 'LIKE', "%{$this->search}%");
        }

        return $query;
    }

    public function setParams(string $search)
    {
        $this->search = $search;
    }
}
