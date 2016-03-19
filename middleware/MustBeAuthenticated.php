<?php namespace Filip\Blog\Middleware;

class MustBeAuthenticated {
  function __invoke($req, $res, $next) {
    $id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if (! $id) {
      return $res->withStatus(302)->withHeader('Location', URL_ROOT . '/users/login?back=' . urlencode($_SERVER['REQUEST_URI']));
    }
    return $next($req, $res);
  }
}
