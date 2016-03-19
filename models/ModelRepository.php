<?php namespace Filip\Blog\Models;

interface ModelRepository {
  public function all();
  public function findById(int $id);
  public function save(Model &$model);
  public function delete(Model $model);
}
