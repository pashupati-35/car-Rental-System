<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate($perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function find($id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail($id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->update($data);
        return $model;
    }

    public function delete($id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function where($column, $operator = null, $value = null)
    {
        if (is_null($value)) {
            return $this->model->where($column, $operator);
        }
        return $this->model->where($column, $operator, $value);
    }

    public function whereIn($column, array $values)
    {
        return $this->model->whereIn($column, $values);
    }

    public function with($relations)
    {
        return $this->model->with($relations);
    }

    public function get()
    {
        return $this->model->get();
    }

    public function first()
    {
        return $this->model->first();
    }

    public function count(): int
    {
        return $this->model->count();
    }
}
