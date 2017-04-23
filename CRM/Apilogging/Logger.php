<?php

class CRM_Apilogging_Logger {

  /**
   * @var string full filesystem path to log file to use
   */
  protected $logFile;

  /**
   * CRM_Apilogging_Logger constructor.
   */
  public function __construct() {
    // TODO: find a better way to determine this path
    $this->logFile = __DIR__ . '/../../../../ConfigAndLog/Api.log';

    if (!file_exists($this->logFile)) {
      touch($this->logFile);
    }
  }

  /**
   * We only want to log API requests from the REST interface, and we only want
   * to do it once for each call. This function returns TRUE whenever we want
   * to perform a log action.
   *
   * @param array $apiRequest
   * @return bool
   */
  protected function logIsNecessary($apiRequest) {
    $hasKeys = !empty($_REQUEST['key']) && !empty($_REQUEST['api_key']);
    if ($hasKeys && empty($GLOBALS['apilogging_logged'])) {
      $GLOBALS['apilogging_logged'] = TRUE;
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * The top-level function that handles everything needed to do the logging
   *
   * @param array $apiRequest
   */
  public function logAPIRequest($apiRequest) {
    if ($this->logIsNecessary($apiRequest)) {
      $this->writeLogFile();
    }
  }

  /**
   * Return an assoc array of things we want to log
   *
   * @return array
   */
  protected function getLogValues () {
    $logValues = $_REQUEST;
    if (!empty($_REQUEST['json'])) {
      $json = json_decode($_REQUEST['json'], TRUE);
      $logValues = array_merge($logValues, $json);
    }
    CRM_Utils_Array::remove($logValues, array('key', 'api_key', 'json'));
    return $logValues;
  }

  /**
   * Write log values to our log file. We write it as JSON so that (if needed)
   * you can parse the log file easily.
   */
  protected function writeLogFile() {
    $content = '"' . self::timeString() . '": ';
    $content .= json_encode(self::getLogValues(), JSON_PRETTY_PRINT) . "," . PHP_EOL;
    $content .= PHP_EOL . PHP_EOL;
    file_put_contents($this->logFile, $content, FILE_APPEND);
  }

  /**
   * Return a string which represents the current time. Format is ISO 8601 plus
   * microseconds
   *
   * @return string
   */
  protected function timeString() {
    $time = array_combine(array('microseconds', 'seconds'), explode(" ", microtime()));
    $d = new DateTime('@' . $time['seconds']);
    return $d->format('Y-m-d H:i:s')
      . ltrim($time['microseconds'], "0")
      . $d->format('P');
  }

}
