<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface RepositoryInterface
{
    public function get();

    public function all();

    public function find($id);

    public function findOrFail($id);

    public function findByField($field, $value);

    public function findWhere(array $where);

    public function findWhereFirst(array $where);

    public function paginate(int $perPage = 10);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
    public function with(array $relations);

    public function sort(string $column, $direction = 'asc');

    public function withCriteria(...$criteria);
}
