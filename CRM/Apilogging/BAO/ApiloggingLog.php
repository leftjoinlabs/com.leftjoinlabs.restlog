<?php

class CRM_Apilogging_BAO_ApiloggingLog extends CRM_Apilogging_DAO_ApiloggingLog {

  /**
   * Create a new ApiloggingLog based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Apilogging_DAO_ApiloggingLog|NULL
   */
  public static function create($params) {
    $entityName = 'ApiloggingLog';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new CRM_Apilogging_DAO_ApiloggingLog();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  public static function getUniqueContacts() {
    $query = "
      select
        al.calling_contact_id as `value`,
        contact.display_name as `label`
      from civicrm_apilogginglog al
      join civicrm_contact contact on contact.id = al.calling_contact_id
      group by al.calling_contact_id;";
    return CRM_Core_DAO::executeQuery($query)->fetchAll();
  }

  public static function getUniqueFieldValues($field) {
    $validFields = array(
      "action",
      "entity",
    );
    if (!in_array($field, $validFields)) {
      throw new Exception('Invalid field');
    }
    $query = "
      select
        $field as `value`,
        $field as `label`
      from civicrm_apilogginglog
      group by $field;";
    return CRM_Core_DAO::executeQuery($query)->fetchAll();
  }

}
