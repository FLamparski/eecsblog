<?php namespace Filip\Blog\Controllers;

use \Filip\Blog\Persist\{PostRepository,UserRepository};

class HomeController {
  use ArticleDisplayTrait;
  private $posts;
  private $users;
  private $twig;

  public function __construct(PostRepository $posts, UserRepository $users, \Twig_Environment $twig) {
    error_log('HomeController::new');
    $this->posts = $posts;
    $this->users = $users;
    $this->twig = $twig;
  }

  public function getAll() {
    return ['posts' => $this->linkToAuthors($this->posts->all())];
  } 
  public function home() {
    return $this->twig->render('home.twig', $this->getAll());
  }
  public function home_json() {
    return json_encode($this->getAll());
  }
}
