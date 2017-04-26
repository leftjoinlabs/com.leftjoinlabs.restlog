<?php

require_once 'apilogging.civix.php';

/**
 * Implements hook_civicrm_apiWrappers().
 */
function apilogging_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  $wrappers[''] = new CRM_Apilogging_APIWrapper();
}

/**
 * inplements hook_civicrm_entityTypes().
 */
function apilogging_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['CRM_Apilogging_DAO_ApiloggingLog'] = array(
    'name' => 'ApiloggingLog',
    'class' => 'CRM_Apilogging_DAO_ApiloggingLog',
    'table' => 'civicrm_apilogginglog',
  );
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function apilogging_civicrm_config(&$config) {
  _apilogging_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function apilogging_civicrm_xmlMenu(&$files) {
  _apilogging_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function apilogging_civicrm_install() {
  _apilogging_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function apilogging_civicrm_postInstall() {
  _apilogging_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function apilogging_civicrm_uninstall() {
  _apilogging_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function apilogging_civicrm_enable() {
  _apilogging_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function apilogging_civicrm_disable() {
  _apilogging_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function apilogging_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _apilogging_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function apilogging_civicrm_managed(&$entities) {
  _apilogging_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function apilogging_civicrm_caseTypes(&$caseTypes) {
  _apilogging_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function apilogging_civicrm_angularModules(&$angularModules) {
  _apilogging_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function apilogging_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _apilogging_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function apilogging_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function apilogging_civicrm_navigationMenu(&$menu) {
  _apilogging_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'com.cividesk.apilogging')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _apilogging_civix_navigationMenu($menu);
} // */
