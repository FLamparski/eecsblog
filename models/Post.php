<?php namespace Filip\Blog\Models;

class Post extends Model {
  public $title;
  public $content;
  public $published;
  public $author;
  public $slug;

  public function permalink() {
    return self::urlFor(get_class($this), $this->slug);
  }
  
  public function canSave($field) {
    if ($field === 'slug') return !$this->id; // slug can only be set once
    return in_array($field, ['title', 'content', 'published']);
  }
}
