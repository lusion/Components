<?php

class Request {
  var $uri,
      $port,
      $method,
      $ip,
      $country,
      $get,
      $post;

  function __construct($options) {
    foreach (array('uri', 'ip', 'port', 'method', 'country', 'get', 'post') as $param) {
      $this->$param = ARR($options, $param);
    }

    // Trim additional params off the uri
    if (($pos = strpos($this->uri,'?')) !== False) {
      $this->uri = substr($this->uri, 0, $pos);
    }
  }

  private static $currentRequest = null;
  static function interpretCurrent() {
    if (!$currentRequest) {
      $currentRequest = new Request(array(
        'uri' => $_SERVER['REQUEST_URI'],
        'host' => ARR($_SERVER, 'HTTP_HOST'),
        'port' => ARR($_SERVER, 'SERVER_PORT'),
        'method' => ARR($_SERVER, 'REQUEST_METHOD'),
        'ip' => find_remote_ip(),

      ));
    }
      


  }

  function isSecure() {
    return $this->port === 443;
  }

  function isPost() {
    return $this->method === 'POST';
  }


  function getURL() {
    return ($this->isSecure() ? 'https' : 'http') . '://' . $this->host . $this->uri;
  }

  function getUri() {
    return new Uri(substr($this->uri, 1) ?: 'index');
  }

  function getCountry() {
    if (!$this->country) {
    }elseif (isset($_GET['country'])) {
      $this->country = $_GET['country'];
      setcookie('country', $this->country, 0, '/');
    }elseif (isset($this->cookie['country'])) {
      self::$country = $this->cookie['country'];
    }elseif ($this->ip) {
      try { 
        self::$country = geoip_country_code_by_name($this->ip);
      } catch (Exception $e) {
        self::$country = 'US';
      }
      setcookie('country', $this->country, 0, '/');
    }
    if (!preg_match('/^[A-Z]{2}$/', $this->country)) {
      $this->country = 'US';
    }
    return $this->country;
  }

  static function permanentRedirect($url) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $url"); 
  }
}
