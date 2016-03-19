<?php namespace Filip\Blog\Controllers;

trait ArticleDisplayTrait {
  private $users;

  public function linkToAuthors(array $posts) {
    $authorCache = [];
    $users = $this->users;
    return array_map(function($post) use (&$authorCache, &$users) {
      if (!isset($authorCache[$post->author])) {
        $author = $users->findById($post->author);
        $authorCache[$post->author] = $author;
      }
      $post->author = $authorCache[$post->author];
      return $post;
    }, $posts);
  }
}
