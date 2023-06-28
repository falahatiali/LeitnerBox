<?php

namespace App\Repositories\Eloquent;

use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\RepositoryAbstract;

class PermissionRepository extends RepositoryAbstract implements PermissionRepositoryInterface
{
    protected function entity(): string
    {
        return Permission::class;
    }
}
