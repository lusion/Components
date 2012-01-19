<?php

class Uri {
  private $parts;

  function __construct($uri) {
    if (is_string($uri)) {
      if ($uri[0] == '/') $uri = substr($uri, 1);
      $uri = explode('/', $uri);
    }
    $this->parts = $uri;
  }

  function get() {
    return '/'.implode('/', $this->parts);
  }
  function getParts() {
    return $this->parts;
  }

  static function split($uri) {
    if (!$uri instanceof Uri) {
      $uri = new Uri($uri);
    }
    return $uri->getParts();
  }
}

