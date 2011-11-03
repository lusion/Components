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

  static function interpretRequest($uri=NULL) {
    if (!$uri) {
      $b = strrchr($_SERVER['PHP_SELF'],'/');
      $uri = trim(substr($_SERVER['REQUEST_URI'],(strlen($_SERVER['PHP_SELF'])-strlen($b)+1)));
    }

    $display = urldecode($uri);
    $qpos = strpos($display,'?');
    if ($qpos !== FALSE)
      $display = substr($display,0,$qpos);
    if (strlen($display) == 0) $display = 'index';

    return new Uri($display);
  }
}

