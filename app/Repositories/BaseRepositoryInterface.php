<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function all();
    public function paginate($perPage = 15);
    public function find($id);
    public function findOrFail($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function where($column, $operator = null, $value = null);
    public function whereIn($column, array $values);
    public function with($relations);
    public function get();
    public function first();
    public function count();
}
