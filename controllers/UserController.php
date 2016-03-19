<?php namespace Filip\Blog\Controllers;

use \Filip\Blog\Persist\{PostRepository,UserRepository};

class UserController {
  use ArticleDisplayTrait;
  private $posts;
  private $users;
  private $twig;

  public function __construct(PostRepository $posts, UserRepository $users, \Twig_Environment $twig) {
    error_log('UserController::new');
    $this->posts = $posts;
    $this->users = $users;
    $this->twig = $twig;
  }

  public function getUser($id) {
    $user = $this->users->findById($id);
    $posts = $this->posts->findByAuthor($id);
    $user->posts = $posts;
    ob_start();
    var_dump($user);
    error_log(ob_get_clean());
    return $user;
  }

  public function profile($id) {
    return $this->twig->render('user.twig', ['user' => $this->getUser($id)]);
  }

  public function showLogin($request) {
    return $this->twig->render('login.twig', [
      'csrf_name' => $request->getAttribute('csrf_name'),
      'csrf_value' => $request->getAttribute('csrf_value'),
      'back' => $request->getQueryParams()['back'] ?? URL_ROOT
    ]);
  }

  public function login($request, $response) {
    $body = $request->getParsedBody();
    $user = $this->users->findByEmail($body['email']);
    if (!$user || !password_verify($body['password'], $user->password)) {
      return $response->withStatus(403)->write($this->twig->render('login.twig', [
        'error' => 'Email or password incorrect',
        'csrf_name' => $request->getAttribute('csrf_name'),
        'csrf_value' => $request->getAttribute('csrf_value')
      ]));
    }
    $_SESSION['user_id'] = $user->id;
    $_SESSION['logged_in_at'] = (new \DateTime)->format(\DateTime::ATOM);
    session_write_close();
    $returnPath = isset($body['back']) ? $body['back'] : URL_ROOT;
    return $response->withStatus(302)->withHeader('Location', $returnPath);
  }
}
