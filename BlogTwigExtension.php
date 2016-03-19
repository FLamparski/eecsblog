<?php namespace Filip\Blog;

use \Twig_Extension;
use \Twig_SimpleFunction;
use \Filip\Blog\Models\Model;

class BlogTwigExtension extends Twig_Extension {
  public function getName() {
    return 'filip-blog';
  }

  public function getFunctions() {
    return [
      new Twig_SimpleFunction('urlFor', function(string $model, ...$params) {
        return call_user_func_array('Filip\\Blog\\Models\\' . $model . '::urlFor', $params);
      }),
      new Twig_SimpleFunction('urlFor', function(Model $mdl) {
        return $mdl->permalink();
      })
    ];
  }
}
