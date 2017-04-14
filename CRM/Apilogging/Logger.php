<?php

class CRM_Apilogging_Logger {

  /**
   * @var string full filesystem path to log file to use
   */
  protected $logFile;

  public function __construct() {
    // TODO: find a better way to determine this path
    $this->logFile = __DIR__ . '/../../../../ConfigAndLog/Api.log';

    if (!file_exists($this->logFile)) {
      touch($this->logFile);
    }
  }

  public function logAPIRequest($apiRequest) {
    $content = json_encode($apiRequest) . "\n\n\n";
    file_put_contents($this->logFile, $content, FILE_APPEND);
  }

}
