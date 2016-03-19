<?php namespace Filip\Blog\Persist;

use Filip\Blog\Models\User;

class UserRepository extends PDORepository {
  protected function getTableName(): string { return 'users'; }
  protected function getModelClass(): string { return User::class; }

  public function __construct(\PDO $pdo) {
    error_log('UserRepository::new');
    parent::__construct($pdo);
  }

  public function findByEmail(string $email) {
    $stmt = $this->pdo->prepare('select * from users where email = ?');
    $stmt->execute([$email]);
    $result = $stmt->fetchAll(\PDO::FETCH_CLASS, $this->getModelClass());
    return $result ? $result[0] : null;
  }

}
