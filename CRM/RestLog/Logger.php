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
    $logDir = CRM_Core_Config::singleton()->configAndLogDir;
    $this->logFile = $logDir . DIRECTORY_SEPARATOR . 'Api.log';
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
    $logValues = array();

    // Grab parameters from request and make sure 'entity' and 'action' are
    // present as keys
    $defaultParameters = array('entity' => '', 'action' => '');
    $parameters = array_merge($defaultParameters, $_REQUEST);

    // Remove sensitive info from $parameters
    CRM_Utils_Array::remove($parameters, array('key', 'api_key'));

    // Flatten params inside of 'json', if present
    if (!empty($_REQUEST['json'])) {
      $json = json_decode($_REQUEST['json'], TRUE);
      $parameters = array_merge($parameters, $json);
      CRM_Utils_Array::remove($parameters, array('json'));
    }

    $logValues['time_stamp'] = $this->timeString();

    // Move 'entity' from $parameters to $logValues
    $logValues['entity'] = strtolower($parameters['entity']);
    CRM_Utils_Array::remove($parameters, 'entity');

    // Move 'action' from $parameters to $logValues
    $logValues['action'] = strtolower($parameters['action']);
    CRM_Utils_Array::remove($parameters, 'action');

    $logValues['calling_contact'] = $this->getCallingContact();
    $logValues['parameters'] = $parameters;

    return $logValues;
  }

  /**
   * Write log values to our log file. We write it as JSON so that (if needed)
   * you can parse the log file easily.
   *
   * @param array $logValues
   */
  protected function writeLogFile($logValues) {
    $content = json_encode($logValues, JSON_PRETTY_PRINT) . ","
      . PHP_EOL . PHP_EOL . PHP_EOL;
    file_put_contents($this->logFile, $content, FILE_APPEND);
  }

  /**
   * Write log values to the database
   *
   * @param array $logValues
   */
  protected function writeLogDB($logValues) {
    CRM_Apilogging_BAO_ApiloggingLog::create(array(
      'time_stamp' => $logValues['time_stamp'],
      'calling_contact_id' => $logValues['calling_contact']['id'],
      'entity' => $logValues['entity'],
      'action' => $logValues['action'],
      'parameters' => json_encode($logValues['parameters']),
    ));
  }

  /**
   * Return a string which represents the current time, in simple ISO 8601
   * format (like MySQL uses)
   *
   * @return string
   */
  protected function timeString() {
    $d = new DateTime();
    return $d->format('Y-m-d H:i:s');
  }

}
