<?php

class Uri implements Countable {
  private $parts;

  function __construct($uri) {
    if (is_string($uri)) {
      if ($uri[0] == '/') $uri = substr($uri, 1);
      $uri = explode('/', $uri);
    }
    $this->parts = $uri;
  }

  /***
   * Count the number of parts
   **/
  function count() {
    return count($this->parts);
  }

  /***
   * Get the uri again
   **/
  function get() {
    return '/'.implode('/', $this->parts);
  }

  /***
   * Get parts of the Uri
   **/
  function getPart($i) {
    return ARR($this->parts, $i);
  }
  function getParts() {
    return $this->parts;
  }

  /***
   * Split a given uri into parts
   **/
  static function split($uri) {
    if (!$uri instanceof Uri) {
      $uri = new Uri($uri);
    }
    return $uri->getParts();
  }
}

