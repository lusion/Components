<?php

class Request {
  static private $country = NULL;

  static function isSecure() {
    if (isset($_SERVER['SERVER_PORT']) && $_SERVER["SERVER_PORT"]==443) {
      return True;
    }else return False;
  }
  static function isPost() {
    return ($_SERVER['REQUEST_METHOD'] == 'POST');
  }

  static function getCountry() {
    if (!self::$country) {
      if (isset($_GET['country'])) {
        self::$country = $_GET['country'];
        setcookie('country', self::$country, 0, '/');
      }elseif (isset($_COOKIE['country'])) {
        self::$country = $_COOKIE['country'];
      }else{
        $remote = find_remote_ip();
        try { 
          self::$country = geoip_country_code_by_name($remote);
        } catch (Exception $e) {
          self::$country = 'US';
        }
        setcookie('country', $country, 0, '/');
      }

      if (!preg_match('/^[A-Z]{2}$/', self::$country)) {
        self::$country = 'US';
      }
    }

    return self::$country;
  }
}
