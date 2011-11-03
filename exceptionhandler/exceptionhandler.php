<?php

class ExceptionHandler {
  private static $lastTrackedError = NULL;
  private static $callback;

  /***
   * PHP callbacks
   **/
  static function exceptionCallback($exception) {
    $cb = self::$callback;
    try {
      $cb($exception);
    } catch (Exception $e) {
    }
    exit(1);
  }
  static function errorCallback($errno, $errstr, $errfile, $errline, $errcontext) {
    self::$lastTrackedError = $errstr;
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
  }
  static function shutdownCallback() {
    $error = error_get_last();
    if ($error && $error['message'] != self::$lastTrackedError) {
      $exception = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
      self::exceptionCallback($exception);
    }
  }

  /***
   * Setup handlers for errors, exceptions and shutdown
   **/
  static function setup($callback) {
    set_exception_handler(array('ExceptionHandler', 'exceptionCallback'));
    register_shutdown_function(array('ExceptionHandler', 'shutdownCallback'));
    set_error_handler(array('ExceptionHandler', 'errorCallback'));

    self::$callback = $callback;
  }
}
