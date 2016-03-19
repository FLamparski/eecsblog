<?php namespace Filip\Blog\Persist;

use \PDO;
use Filip\Blog\Models\{Model,ModelRepository};

abstract class PDORepository implements ModelRepository {
  protected abstract function getTableName(): string;
  protected abstract function getModelClass(): string;

  protected $pdo;
  public function __construct(PDO $pdo) {
    $idCache = [];
    $this->pdo = $pdo;
  }

  public function all() {
    $sql = 'select * from ' . $this->getTableName();
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_CLASS, $this->getModelClass());
  }

  public function findById(int $id) {
    $sql = 'select * from ' . $this->getTableName() . ' where id = :id limit 1';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $results = $stmt->fetchAll(PDO::FETCH_CLASS, $this->getModelClass());
    return $results ? $results[0] : null;
  }

  public function save(Model &$model) {
    $reflect = new \ReflectionClass(get_class($model));
    $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
    $isInsert = !($model->id);
    $sql = ($isInsert ? 'insert into' : 'update') . ' ' . $this->getTableName() . ' set ';

    $fieldsToSave = array_filter($props, function(\ReflectionProperty $prop) use ($model) {
      return $model->canSave($prop->name) && $prop->name !== 'id';
    });
    $setFields = array_map(function(\ReflectionProperty $prop) {
      return sprintf("%s = :%s", $prop->name, $prop->name);
    }, $fieldsToSave);

    $params = array_reduce($fieldsToSave, function(array $params, \ReflectionProperty $prop) use ($model) {
      $v = $model->{$prop->name};
      if (is_bool($v)) $v = +$v;
      return array_merge($params, [$prop->name => $v]);
    }, []);
    $setClause = implode(', ', $setFields);
    $sql .= $setClause;
    if (!$isInsert) {
      $sql .= ' where id = :id';
      $params['id'] = $model->id;
    }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    if ($isInsert) {
      $model->id = $this->pdo->lastInsertId();
    }
  }

  public function delete(Model $model) {
    $sql = 'delete from ' . $this->getTableName() . ' where id = :id limit 1';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $model->id]);
  }
}
