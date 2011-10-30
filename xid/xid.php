<?php
/**
 * Namespace for Xid operations
 */
class Xid {
  const REGEX = '[0-9A-Za-z-_:]*';

  private static $base64_number_to_symbol =
    'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';


  private static $base64_symbol_to_number = NULL; 
  /**
   * @param int $symbol Base64 url-encode variant symbol
   * @return int Numerical value of symbol
   */
  private static function base64_decode_symbol($symbol) {
    // create hash table of symbols to numerical values
    if (self::$base64_symbol_to_number == NULL) {
      self::$base64_symbol_to_number
        = array_flip(str_split(self::$base64_number_to_symbol));
    }
    return self::$base64_symbol_to_number[$symbol];
  }
  /**
   * Direct conversion of Base 64 to number.
   * @param $number Base64 to decode.
   * @return int Numerical value of $number.
   */
  public static function base64_decode_number($number) {
    $sum = 0;
    $mul = 0;
    for ($i = strlen($number)-1; $i >= 0; $i--) {
      $sum += self::base64_decode_symbol($number[$i])*pow(64, $mul++);
    }
    return $sum;
  }
  /**
  * @param int $number Number from 0 to 63
  * @return Base64 symbol corresponding to $number
  * @see base64_number
  */
  private static function base64_encode_symbol($number) {
    return self::$base64_number_to_symbol[$number];
  }
  /**
  * Direct conversion of number to Base64. 
  * @param int $number Number to convert to Base64
  * @return string Base64 encoding of $number
  */
  public static function base64_encode_number($number) {
    if ($number == 0) {
      return 'A';
    }
    $output = '';
    while ($number) {
      $output = self::base64_encode_symbol($number%64) . $output;
      $number >>= 6;
    }
    return $output;
  }
  /**
   * Encode a number to Base64 directly and pad with leading 0's (A's)
   * @param int $number The number to encode.
   * @param int $length Length of returned string, padded to length 
   * @return Padded, Base64-encoded version of $number 
   */
  public static function base64_encode_number_pad($number, $length) {
    return str_pad(self::base64_encode_number($number), $length,
      "A", STR_PAD_LEFT);
  }
  /**
   * Create a distributed xid from given reseller id and object id.
   * Table is implicit in class name used for load().
   * @return string 10-symbol padded Base 64 value containing both id's.
   */
  public static function encode($reseller_id, $object_id) {
    return self::base64_encode_number($reseller_id).':'.
      self::base64_encode_number($object_id);
  }

  /**
   * @return array($reseller_id, $object_id)
   */
  public static function decode($xid) {
    // Allow standard numeric ID for now
    if (is_numeric($xid)) return array(0, intval($xid));
    list($reseller_portion, $object_portion) = explode(':', $xid);
    return array(self::base64_decode_number($reseller_portion),
      self::base64_decode_number($object_portion));
  }

  /**
   * Test it looks like an XID
   * @return boolean/string Return the candidate if valid, or False otherwise
   */ 
  public static function test($candidate) {
    // Allow standard numeric id or base64:base64 style
    if ($candidate && 
        (is_int($candidate) || // plain integer candidate
        (is_string($candidate) && preg_match('/^([0-9]+|[A-Za-z0-9-_]+:[A-Za-z0-9-_]+)$/S', $candidate))))
    {
      return $candidate;
    }
    return False;
  }
}

