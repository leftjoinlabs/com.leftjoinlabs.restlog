<?php

class CRM_Apilogging_APIWrapper implements API_Wrapper {

  /**
   * the wrapper contains a method that allows you to alter the parameters of
   * the api request (including the action and the entity)
   */
  public function fromApiInput($apiRequest) {
    $logger = new CRM_Apilogging_Logger();
    $logger->logAPIRequest($apiRequest);
    return $apiRequest;
  }

  /**
   * alter the result before returning it to the caller.
   */
  public function toApiOutput($apiRequest, $result) {
    return $result;
  }

}
