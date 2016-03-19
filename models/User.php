<?php namespace Filip\Blog\Models;

class User extends Model {
  public $email;
  public $password;
  public $name;
  public $image;
  public $tagline;
  
  public function getImageUrl() {
    return USER_IMAGES . $this->image;
  }
}
