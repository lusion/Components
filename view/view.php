<?php

/***
 * Load views based on directory structure
 **/
class View {

  function __construct($path, $extra) {
    $this->path = $path;
    $this->extra = $extra;
  }

  function render() {
    $extra = $this->extra;
    require $this->path;
  }

  static function find($uri, $path='views') {
    $uri = Uri::split($uri);

    $path .= '/';
    $extra = $uri;

		while ($extra) {
      if ($extra[0] == '.') break;

      if (file_exists($path . $extra[0].'.php')) {
        $filename = array_shift($extra);
        return new View($path . $filename .'.php', $extra);
      }elseif (is_dir($path . $extra[0])) {
        $path .= array_shift($extra).'/';
        continue;
      }elseif (file_exists($path . 'index.php')) {
        return new View($path . 'index.php', $extra);
      }else{
        break;
      }
    }

    if (file_exists($path . 'index.php')) {
      return new View($path . 'index.php', $extra);
    }

    return NULL;
  }

  static function findExact($uri, $path='views') {
    if ($view = self::find($uri, $path)) {
      if (!$view->extra) {
        return $view;
      }
    }
    return NULL;
  }

  static function display($uri, $path='views') {
    if ($view = self::find($uri, $path)) {
      $view->render();
    }else{
      throw new Exception('Could not find view: '.implode('/', $uri).' in '.$path);
    }
  }
}
