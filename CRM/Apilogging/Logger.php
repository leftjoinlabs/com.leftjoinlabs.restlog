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
   * @param array $apiRequest
   * @return bool
   */
  protected function logIsNecessary($apiRequest) {
    $isGeneric = !empty($apiRequest['is_generic']) && $apiRequest['is_generic'];
    $hasKeys = !empty($_REQUEST['key'] )&& !empty($_REQUEST['api_key']);
    return $isGeneric && $hasKeys;
  }

  /**
   * @param array $apiRequest
   */
  public function logAPIRequest($apiRequest) {
    if ($this->logIsNecessary($apiRequest)) {
      $this->writeLog();
    }
  }

  protected function writeLog() {
    $content = "[" . date('c') . "]" . PHP_EOL;
    $logValues = $_REQUEST;
    CRM_Utils_Array::remove($logValues, array('key', 'api_key'));
    foreach ($logValues as $k => $v) {
      $content .= "$k: $v" . PHP_EOL;
    }
    $content .= PHP_EOL . PHP_EOL;
    file_put_contents($this->logFile, $content, FILE_APPEND);
  }

}
