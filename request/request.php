<?php

class Request {
  static function isSecure() {
    if (isset($_SERVER['SERVER_PORT']) && $_SERVER["SERVER_PORT"]==443) {
      return True;
    }else return False;
  }
  static function isPost() {
    return ($_SERVER['REQUEST_METHOD'] == 'POST');
  }
}
