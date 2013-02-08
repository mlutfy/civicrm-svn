<?php
/**
 * File for the CiviCRM APIv3 API wrapper
 *
 * @package CiviCRM_APIv3
 * @subpackage API
 *
 * @copyright CiviCRM LLC (c) 2004-2013
 */

/* This api exception returns more information than the default one. The aim it let the api consumer know better what is exactly the error without having to parse the error message.
 * If you consume an api that doesn't return an error_code or the extra data you need, consider improving the api and contribute 
 * @param string $message 
 *   the human friendly error message
 * @param string $error_code
 *   a computer friendly error code. By convention, no space (but underscore allowed)
 *  ex: mandatory_missing, duplicate, invalid_format
 * @param array $data
 *   extra params to return. eg an extra array of ids. It is not mandatory, but can help the computer using the api. Keep in mind the api consumer isn't to be trusted. eg. the database password is NOT a good extra data 
 */
class API_Exception extends Exception
{
  private $extraParams = array();
  public function __construct($message, $error_code = 0, $extraParams = array(),Exception $previous = null) {
    if (is_numeric ($error_code)) // using int for error code "old way")
      $code = $error_code;
    else
      $code=0;
    parent::__construct(ts($message), $code, $previous);
    $this->extraParams = $extraParams + array('error_code' => $error_code);
  }

  // custom string representation of object
  public function __toString() {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function getExtraParams() {
    return $this->extraParams;
  }

  public function getErrorCodes(){ 
    return array(
        2000 => '$params was not an array',
        2001 => 'Invalid Value for Date field',
        2100 => 'String value is longer than permitted length'
        );
  }
}
