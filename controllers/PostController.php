<?php namespace Filip\Blog\Controllers;

use \Filip\Blog\Persist\{PostRepository,UserRepository};
use \Filip\Blog\Models\Post;

class PostController {
  use ArticleDisplayTrait;
  private $posts;
  private $users;
  private $twig;

  public function __construct(PostRepository $posts, UserRepository $users, \Twig_Environment $twig) {
    error_log('PostController::new');
    $this->posts = $posts;
    $this->users = $users;
    $this->twig = $twig;
  }

  public function one(string $slug) {
    return $this->twig->render('home.twig', ['posts' => $this->linkToAuthors($this->posts->findBySlug($slug))]);
  }
  
  public function showEditor($req, string $slug = '') {
    if ($slug) {
      $post = $this->posts->findBySlug($slug, false)[0];
    } else {
      $post = new Post;
    }
    error_log(print_r($post, true));
    return $this->twig->render('edit.twig', ['post' => $post, 'csrf_name' => $req->getAttribute('csrf_name'), 'csrf_value' => $req->getAttribute('csrf_value')]);
  }
  
  public function save($request, $response) {
    $body = $request->getParsedBody();
    $post = new Post;
    $post->id = $body['id'] ?? null;
    $post->slug = $body['slug'];
    $post->title = $body['title'];
    $post->content = $body['content'];
    $post->published = isset($body['publish']) ? true : false;
    $post->author = $_SESSION['user_id'];
    $this->posts->save($post);
    return $response->withStatus(302)->withHeader('Location', URL_ROOT . 'posts/' . $body['slug']);
  }
}
