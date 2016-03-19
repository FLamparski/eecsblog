<?php namespace Filip\Blog\Models;

class Model {
  public $id;
  public $createdAt;
  public $modifiedAt;

  public static function urlFor($class, ...$params) {
    $cls = new \ReflectionClass($class);
    $name = $cls->getShortName();

    return (URL_ROOT ?? '/') . mb_strtolower($name) . 's/' . implode('/', $params);
  }

  public function permalink() {
    return self::urlFor(get_class($this), $this->id);
  }
}
