<?php

/**
 * ApiloggingLog.Getuniquevalues API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_apilogging_log_Getuniquevalues_spec(&$spec) {
  $spec['field']['api.required'] = 1;
}

/**
 * ApiloggingLog.Getuniquevalues API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_apilogging_log_Getuniquevalues($params) {
  if ($params['field'] == 'calling_contact') {
    $result = CRM_Apilogging_BAO_ApiloggingLog::getUniqueContacts();
  }
  else {
    $result = CRM_Apilogging_BAO_ApiloggingLog::getUniqueFieldValues($params['field']);
  }
  return civicrm_api3_create_success($result, $params, 'ApiloggingLog', 'getuniquevalues');
}
