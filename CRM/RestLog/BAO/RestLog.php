<?php

class CRM_RestLog_BAO_RestLog extends CRM_RestLog_DAO_RestLog {

  /**
   * Create a new RestLog based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_RestLog_DAO_RestLog|NULL
   */
  public static function create($params) {
    $entityName = 'RestLog';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new CRM_RestLog_DAO_RestLog();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  /**
   * Answers the question "which contacts have entries in the restlog table?"
   *
   * @return array
   *   e.g. [
   *     ["value" => 101, "label" => "Bart Simpson"],
   *     ["value" => 102, "label" => "Lisa Simpson"],
   *     ["value" => 103, "label" => "Maggie Simpson"],
   *   ]
   */
  public static function getUniqueContacts() {
    $query = "
      select
        rl.calling_contact_id as `value`,
        contact.display_name as `label`
      from civicrm_restlog rl
      join civicrm_contact contact on contact.id = rl.calling_contact_id
      group by rl.calling_contact_id;";
    return CRM_Core_DAO::executeQuery($query)->fetchAll();
  }

  /**
   * Returns an array of unique values in the restlog table
   *
   * @param string $field
   *   e.g. "entity"
   *
   * @return array of strings
   *   e.g. ["contact", "contribution", "activity"]
   *
   * @throws \Exception
   *   When supplied field is not valid. This is used to prevent SQL injection.
   */
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
      from civicrm_restlog
      group by $field;";
    return CRM_Core_DAO::executeQuery($query)->fetchAll();
  }

}
