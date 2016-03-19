<?php namespace Filip\Blog\Persist;

use \Filip\Blog\Models\Post;

class PostRepository extends PDORepository {
  protected function getTableName(): string { return 'posts'; }
  protected function getModelClass(): string { return Post::class; }

  public function __construct(\PDO $pdo) {
    error_log('PostRepository::new');
    parent::__construct($pdo);
  }

  public function all() {
    $sql = 'select * from posts where published = 1 order by createdAt desc';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(\PDO::FETCH_CLASS, $this->getModelClass());
    return $results;
  }

  public function findBySlug(string $slug, bool $published = true) {
    $sql = 'select * from posts where slug = ?';
    if ($published) $sql .= ' and published = 1';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$slug]);
    return $stmt->fetchAll(\PDO::FETCH_CLASS, $this->getModelClass());
  }

  public function findByAuthor($id) {
    $sql = 'select * from posts where author = ? and published = 1 order by createdAt desc';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetchAll(\PDO::FETCH_CLASS, $this->getModelClass());
  }
}
