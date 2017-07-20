<?php

class CRM_Apilogging_Logger {

  /**
   * @var string full filesystem path to log file to use
   */
  protected $logFile;

  /**
   * The current time with unixtime seconds and microseconds
   * @var array of integers with keys 'microseconds' and 'seconds'
   */
  protected $time;

  /**
   * CRM_Apilogging_Logger constructor.
   */
  public function __construct() {

    // Set up log file
    $logDir = Civi::paths()->getPath('ConfigAndLog');
    $this->logFile = $logDir . DIRECTORY_SEPARATOR . 'Api.log';
    if (!file_exists($this->logFile)) {
      touch($this->logFile);
    }

    // Get current time
    // TODO use timezone of server
    $this->time = array_combine(
        array('microseconds', 'seconds'),
        explode(" ", microtime())
    );
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
      $logValues = $this->getLogValues();
      $this->writeLogFile($logValues);
      $this->writeLogDB($logValues);
    }
  }

  /**
   * Get information about the contact record associated with the API key used
   * to make this API call.
   *
   * @return array assoc with 'id' and 'display_name' as keys
   */
  protected function getCallingContact() {
    $apiKey = CRM_Core_DAO::escapeString($_REQUEST['api_key']);
    $query = CRM_Core_DAO::executeQuery("
      SELECT id, display_name 
      FROM civicrm_contact 
      WHERE api_key = '$apiKey'");
    $result = $query->fetchAll();
    if (!empty($result)) {
      return $result[0];
    }
    else {
      return array('id' => '');
    }
  }

  /**
   * Return an assoc array of things we want to log
   *
   * @return array
   */
  protected function getLogValues() {
    $defaults = array('entity' => '', 'action' => '');
    $logValues = array_merge($defaults, $_REQUEST);
    if (!empty($_REQUEST['json'])) {
      $json = json_decode($_REQUEST['json'], TRUE);
      $logValues = array_merge($logValues, $json);
    }
    CRM_Utils_Array::remove($logValues, array('key', 'api_key', 'json'));
    $logValues['called_by'] = $this->getCallingContact();
    return $logValues;
  }

  /**
   * Write log values to our log file. We write it as JSON so that (if needed)
   * you can parse the log file easily.
   *
   * @param array $logValues
   */
  protected function writeLogFile($logValues) {
    $content = '"' . self::microTimeString() . '": ';
    $content .= json_encode($logValues, JSON_PRETTY_PRINT) . "," . PHP_EOL;
    $content .= PHP_EOL . PHP_EOL;
    file_put_contents($this->logFile, $content, FILE_APPEND);
  }

  /**
   * Write log values to the database
   *
   * @param array $logValues
   */
  protected function writeLogDB($logValues) {
    $parameters = $logValues;
    CRM_Utils_Array::remove($parameters, array(
      'called_by',
      'entity',
      'action',
    ));
    CRM_Apilogging_BAO_ApiloggingLog::create(array(
      'time_stamp' => $this->timeString(),
      'calling_contact_id' => $logValues['called_by']['id'],
      'entity' => strtolower($logValues['entity']),
      'action' => strtolower($logValues['action']),
      'parameters' => json_encode($parameters),
    ));
  }

  /**
   * Return a string which represents the current time, in simple ISO 8601
   * format (like MySQL uses)
   *
   * @return string
   */
  protected function timeString() {
    $d = new DateTime('@' . $this->time['seconds']);
    return $d->format('Y-m-d H:i:s');
  }

  /**
   * Return a string which represents the current time. Format is ISO 8601 plus
   * microseconds
   *
   * @return string
   */
  protected function microTimeString() {
    return $this->timeString()
      . ltrim($this->time['microseconds'], "0")
      . "+00:00";
  }

}
