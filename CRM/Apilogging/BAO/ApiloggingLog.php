<?php

class CRM_Apilogging_BAO_ApiloggingLog extends CRM_Apilogging_DAO_ApiloggingLog {

  /**
   * Create a new ApiloggingLog based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Apilogging_DAO_ApiloggingLog|NULL
   *
  public static function create($params) {
    $className = 'CRM_Apilogging_DAO_ApiloggingLog';
    $entityName = 'ApiloggingLog';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}
