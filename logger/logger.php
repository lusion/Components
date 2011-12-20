<?php

class Logger {
  private $filename;
  private $prefix;

  function __construct($filename, $options=array()) {
    $this->filename = $filename;

    $this->prefix = isset($options['prefix']) ? $options['prefix'] : '';
  }

  function log($message) {
    file_put_contents($this->filename, $this->prefix.$message."\n", FILE_APPEND | LOCK_EX);
  }
}
