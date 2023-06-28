<?php

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\RepositoryAbstract;

class RoleRepository extends RepositoryAbstract implements RoleRepositoryInterface
{
    public function entity()
    {
        return Role::class;
    }
}
