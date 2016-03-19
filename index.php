<?php namespace Filip\Blog;

define('BLOG_ROOT', __DIR__);
define('URL_ROOT', '/blog/');
define('USER_IMAGES', URL_ROOT . 'assets/avatars/');
define('E_REALLY_ALL', -1);

require BLOG_ROOT . '/vendor/autoload.php';

error_reporting(E_REALLY_ALL);

use \Filip\Blog\Persist\{PostRepository,UserRepository};
use \Filip\Blog\Controllers\{HomeController,PostController,UserController};
use \Filip\Blog\Middleware\MustBeAuthenticated;
use \DI\ContainerBuilder;
use function \DI\object;
use function \DI\get;

session_start();

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions([
  \Twig_Environment::class => function() {
    $loader = new \Twig_Loader_Filesystem(BLOG_ROOT . '/assets/templates');
    $twig = new \Twig_Environment($loader);
    //$twig->addExtension(new BlogTwigExtension());
    $twig->addGlobal('site_root', URL_ROOT);
    return $twig;
  },
  \PDO::class => function() {
    $pdo = new \PDO('mysql:host=localhost;dbname=filip_blog;charset=utf8', 'vagrant', 'vagrant', [
      \PDO::ATTR_EMULATE_PREPARES => false,
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ]);
    return $pdo;
  },
  \Slim\Container::class => function() {
    $c = new \Slim\Container([
      'settings' => [
        'displayErrorDetails' => true
      ]
    ]);
    $c['errorHandler'] = function($c) {
      return function($request, $response, $exception) use ($c) {
        error_log('Exception: ' . get_class($exception) . ': ' . $exception->getMessage());
        error_log($exception->getTraceAsString());
        return $c['response']->withStatus(500);
      };
    };
    return $c;
  },
  \Slim\App::class => function($container) {
    $app = new \Slim\App($container->get(\Slim\Container::class));
    $app->add(new \Slim\Csrf\Guard);
    return $app;
  }
]);
$container = $containerBuilder->build();

$app = $container->get(\Slim\App::class);

$app->get('/', function($request, $response) use (&$container) {
  $homeController = $container->get(HomeController::class);
  if (in_array('application/json', $request->getHeader('HTTP_ACCEPT'))) {
    $response->withHeader('content-type', 'application/json');
    $response->write($homeController->home_json());
  } else {
    $response->write($homeController->home());
  }
  return $response;
});
$app->get('/posts/{slug}', function($request, $response, $args) use (&$container) {
  $response->write($container->get(PostController::class)->one($args['slug']));
  return $response;
});


$app->get('/users/login', function($request, $response) use (&$container) {
  return $response->write($container->get(UserController::class)->showLogin($request));
});
$app->post('/users/login', function($request, $response) use (&$container) {
  return $container->get(UserController::class)->login($request, $response);
});
$app->get('/users/logout', function($request, $response) {
  unset($_SESSION['user_id']);
  unset($_SESSION['logged_in_at']);
  session_write_close();
  return $response->withStatus(302)->withHeader('Location', '/blog');
})->add(MustBeAuthenticated::class);

$app->get('/users/{id}', function($request, $response, $args) use (&$container) {
  $response->write($container->get(UserController::class)->profile($args['id']));
  return $response;
});

$app->get('/new_post', function($request, $response) use (&$container) {
  $response->write($container->get(PostController::class)->showEditor($request));
  return $response;
})->add(MustBeAuthenticated::class);
$app->get('/posts/{slug}/edit', function($request, $response, $params) use (&$container) {
  $response->write($container->get(PostController::class)->showEditor($request, $params['slug']));
  return $response;
})->add(MustBeAuthenticated::class);
$app->post('/posts/save', function($request, $response, $params) use (&$container) {
  return $container->get(PostController::class)->save($request, $response);
})->add(MustBeAuthenticated::class);

$app->run();
