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

  static function getRemoteIP() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_X_FORWARDED') as $header) {
      if ($values = ARR($_SERVER, $header)) {
        foreach (explode(',', $values) as $ip) {
          if (valid_ip($ip)) return $ip;
        }
      }
    }

    return ARR($_SERVER,'REMOTE_ADDR','0.0.0.0');
  }

  static function getURL() {
    return (self::isSecure() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  }

  static function setupMixpanelDistinct() {
    if (!$mixpanel_distinct = DEF('mixpanel_distinct')) {
      if (!$mixpanel_distinct = ARR($_COOKIE, 'mixpanel_distinct')) {
        $mixpanel_distinct = uniqid(ip2long($_SERVER['REMOTE_ADDR']), True);
      }
    }
    setcookie('mixpanel_distinct', $mixpanel_distinct, time()+60*60*24*30, '/', '.'.domain);
    return $mixpanel_distinct;
  }

  static function getCountry() {
    if (!self::$country) {
      if (isset($_GET['country'])) {
        self::$country = $_GET['country'];
        setcookie('country', self::$country, 0, '/');
      }elseif (isset($_COOKIE['country'])) {
        self::$country = $_COOKIE['country'];
      }else{
        $remote = self::getRemoteIP();
        try { 
          self::$country = geoip_country_code_by_name($remote);
        } catch (Exception $e) {
          self::$country = 'US';
        }
        setcookie('country', self::$country, 0, '/');
      }

      if (!preg_match('/^[A-Z]{2}$/', self::$country)) {
        self::$country = 'US';
      }
    }

    return self::$country;
  }

  static function permanentRedirect($url) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $url"); 
  }
}
