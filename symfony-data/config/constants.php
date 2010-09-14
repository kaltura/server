<?php

// for tracking temporary variables
$usedVars = array_keys(get_defined_vars());

$sf_root_dir    = sfConfig::get('sf_root_dir');
$sf_app         = sfConfig::get('sf_app');
$sf_environment = sfConfig::get('sf_environment');

sfConfig::add(array(
  // root directory names
  'sf_bin_dir_name'     => $sf_bin_dir_name     = 'batch',
  'sf_cache_dir_name'   => $sf_cache_dir_name   = 'cache',
  'sf_log_dir_name'     => $sf_log_dir_name     = 'log',
  'sf_lib_dir_name'     => $sf_lib_dir_name     = 'lib',
  'sf_web_dir_name'     => $sf_web_dir_name     = 'web',
  'sf_upload_dir_name'  => $sf_upload_dir_name  = 'uploads',
  'sf_data_dir_name'    => $sf_data_dir_name    = 'data',
  'sf_config_dir_name'  => $sf_config_dir_name  = 'config',
  'sf_apps_dir_name'    => $sf_apps_dir_name    = 'apps',
  'sf_test_dir_name'    => $sf_test_dir_name    = 'test',
  'sf_doc_dir_name'     => $sf_doc_dir_name     = 'doc',
  'sf_plugins_dir_name' => $sf_plugins_dir_name = 'plugins',

  // global directory structure
  'sf_app_dir'        => $sf_app_dir = $sf_root_dir.DIRECTORY_SEPARATOR.$sf_apps_dir_name.DIRECTORY_SEPARATOR.$sf_app,
  'sf_lib_dir'        => $sf_lib_dir = $sf_root_dir.DIRECTORY_SEPARATOR.$sf_lib_dir_name,
  'sf_bin_dir'        => $sf_root_dir.DIRECTORY_SEPARATOR.$sf_bin_dir_name,
  'sf_web_dir'        => $sf_root_dir.DIRECTORY_SEPARATOR.$sf_web_dir_name,
  'sf_upload_dir'     => $sf_root_dir.DIRECTORY_SEPARATOR.$sf_web_dir_name.DIRECTORY_SEPARATOR.$sf_upload_dir_name,
  'sf_root_cache_dir' => $sf_root_cache_dir = $sf_root_dir.DIRECTORY_SEPARATOR.$sf_cache_dir_name,
  'sf_base_cache_dir' => $sf_base_cache_dir = $sf_root_cache_dir.DIRECTORY_SEPARATOR.$sf_app,
  'sf_cache_dir'      => $sf_cache_dir      = $sf_base_cache_dir.DIRECTORY_SEPARATOR.$sf_environment,
  'sf_log_dir'        => $sf_root_dir.DIRECTORY_SEPARATOR.$sf_log_dir_name,
  'sf_data_dir'       => $sf_root_dir.DIRECTORY_SEPARATOR.$sf_data_dir_name,
  'sf_config_dir'     => $sf_root_dir.DIRECTORY_SEPARATOR.$sf_config_dir_name,
  'sf_test_dir'       => $sf_root_dir.DIRECTORY_SEPARATOR.$sf_test_dir_name,
  'sf_doc_dir'        => $sf_root_dir.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.$sf_doc_dir_name,
  'sf_plugins_dir'    => $sf_root_dir.DIRECTORY_SEPARATOR.$sf_plugins_dir_name,

  // lib directory names
  'sf_model_dir_name'      => $sf_model_dir_name = 'model',

  // lib directory structure
  'sf_model_lib_dir'  => $sf_lib_dir.DIRECTORY_SEPARATOR.$sf_model_dir_name,

  // SF_CACHE_DIR directory structure
  'sf_template_cache_dir' => $sf_cache_dir.DIRECTORY_SEPARATOR.'template',
  'sf_i18n_cache_dir'     => $sf_cache_dir.DIRECTORY_SEPARATOR.'i18n',
  'sf_config_cache_dir'   => $sf_cache_dir.DIRECTORY_SEPARATOR.$sf_config_dir_name,
  'sf_test_cache_dir'     => $sf_cache_dir.DIRECTORY_SEPARATOR.'test',
  'sf_module_cache_dir'   => $sf_cache_dir.DIRECTORY_SEPARATOR.'modules',

  // SF_APP_DIR sub-directories names
  'sf_app_i18n_dir_name'     => $sf_app_i18n_dir_name     = 'i18n',
  'sf_app_config_dir_name'   => $sf_app_config_dir_name   = 'config',
  'sf_app_lib_dir_name'      => $sf_app_lib_dir_name      = 'lib',
  'sf_app_module_dir_name'   => $sf_app_module_dir_name   = 'modules',
  'sf_app_template_dir_name' => $sf_app_template_dir_name = 'templates',

  // SF_APP_DIR directory structure
  'sf_app_config_dir'   => $sf_app_dir.DIRECTORY_SEPARATOR.$sf_app_config_dir_name,
  'sf_app_lib_dir'      => $sf_app_dir.DIRECTORY_SEPARATOR.$sf_app_lib_dir_name,
  'sf_app_module_dir'   => $sf_app_dir.DIRECTORY_SEPARATOR.$sf_app_module_dir_name,
  'sf_app_template_dir' => $sf_app_dir.DIRECTORY_SEPARATOR.$sf_app_template_dir_name,
  'sf_app_i18n_dir'     => $sf_app_dir.DIRECTORY_SEPARATOR.$sf_app_i18n_dir_name,

  // SF_APP_MODULE_DIR sub-directories names
  'sf_app_module_action_dir_name'   => 'actions',
  'sf_app_module_template_dir_name' => 'templates',
  'sf_app_module_lib_dir_name'      => 'lib',
  'sf_app_module_view_dir_name'     => 'views',
  'sf_app_module_validate_dir_name' => 'validate',
  'sf_app_module_config_dir_name'   => 'config',
  'sf_app_module_i18n_dir_name'     => 'i18n',
));

// Remove temporary variables
foreach (array_diff(array_keys(get_defined_vars()), $usedVars) as $var) {
    unset($$var);
}
