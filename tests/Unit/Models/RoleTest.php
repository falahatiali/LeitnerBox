<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class RoleTest extends TestCase
{
    public function test_it_has_many_to_many_relationship_with_permissions()
    {
        $role = Role::factory()->create();

        $permissions = Permission::factory()->count(3)->create();

        $role->permissions()->sync($permissions->pluck('id'));

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $role->permissions);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $role->permissions());
    }

    public function test_it_has_many_to_many_relationship_with_users()
    {
        $role = Role::factory()->create();

        $users = User::factory()->count(3)->create();

        $role->users()->sync($users->pluck('id'));

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $role->users);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $role->users());
    }
}
