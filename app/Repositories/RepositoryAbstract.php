<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Criteria\CriteriaInterface;
use App\Repositories\Exception\EntityNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class RepositoryAbstract implements RepositoryInterface, CriteriaInterface
{
    protected mixed $entity;

    /**
     * @throws EntityNotFoundException
     */
    public function __construct()
    {
        $this->entity = $this->resolveEntity();
    }

    protected abstract function entity();

    public function get()
    {
        return $this->entity->get();
    }

    public function all()
    {
        return $this->entity->all();
    }

    public function find($id)
    {
        return $this->entity->find($id);
    }

    public function findOrFail($id)
    {
        return $this->entity->findOrFail($id);
    }

    public function findByField($field, $value)
    {
        return $this->entity->where($field, $value)->get();
    }

    public function findWhere(array $where)
    {
        return $this->entity->where($where);
    }

    public function findWhereFirst(array $where)
    {
        return $this->entity->where($where)->first();
    }

    public function paginate(int $perPage = 10)
    {
        return $this->entity->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->entity->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record = $this->findOrFail($id);

        return $record->delete();
    }

    public function with(array $relations)
    {
         $this->entity = $this->entity->with($relations);

         return $this;
    }

    public function sort(string $column, $direction = 'asc')
    {
        return $this->entity->orderBy($column, $direction);
    }

    public function withCriteria(...$criteria): static
    {
        $criteria = Arr::flatten($criteria);

        foreach ($criteria as $criterion) {
            $this->entity = $criterion->apply($this->entity);
        }

        return $this;
    }

    protected function resolveEntity()
    {
        if (!method_exists($this, 'entity')) {
            throw new EntityNotFoundException('Entity not found');
        }

        return app()->make($this->entity());
    }
}
