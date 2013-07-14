<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

pake_desc('upgrade to a new symfony release');
pake_task('upgrade');

pake_desc('downgrade to a previous symfony release');
pake_task('downgrade', 'project_exists');

function run_downgrade($task, $args)
{
  throw new Exception('I have no downgrade script for this release.');
}

function run_upgrade($task, $args)
{
  if (!isset($args[0]))
  {
    throw new Exception('You must provide the upgrade script to use (1.0 to upgrade to symfony 1.0 for example).');
  }

  $version = $args[0];

   if ($version == '1.0')
   {
     run_upgrade_1_0($task, $args);
   }
   else
   {
     throw new Exception('I have no upgrade script for this release.');
   }
}

function run_upgrade_1_0($task, $args)
{
  // check we have a project
  if (!file_exists('symfony') && !file_exists('SYMFONY'))
  {
    throw new Exception('You must be in a symfony project directory');
  }

  // upgrade propel.ini
  _upgrade_1_0_propel_ini();

  // upgrade i18n support
  _upgrade_1_0_i18n();

  // upgrade model classes
  _upgrade_1_0_propel_model();

  // migrate activate to enabled
  _upgrade_1_0_activate();

  // find all applications for this project
  $apps = pakeFinder::type('directory')->name(sfConfig::get('sf_app_module_dir_name'))->mindepth(1)->maxdepth(1)->relative()->in(sfConfig::get('sf_apps_dir_name'));

  // install symfony CLI
  if (file_exists(sfConfig::get('sf_root_dir').'/SYMFONY'))
  {
    pake_remove(sfConfig::get('sf_root_dir').'/SYMFONY', '');
  }
  pake_copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/project/symfony', sfConfig::get('sf_root_dir').'/symfony');
  pake_chmod('symfony', sfConfig::get('sf_root_dir'), 0777);

  // update schemas
  _upgrade_1_0_schemas();

  // add bootstrap files for tests
  _add_1_0_test_bootstraps();

  // upgrade main config.php
  _upgrade_1_0_main_config_php();

  // upgrade all applications
  foreach ($apps as $app_module_dir)
  {
    $app = str_replace(DIRECTORY_SEPARATOR.sfConfig::get('sf_app_module_dir_name'), '', $app_module_dir);
    pake_echo_action('upgrade 1.0', pakeColor::colorize(sprintf('upgrading application "%s"', $app), array('fg' => 'cyan')));

    $app_dir = sfConfig::get('sf_apps_dir_name').'/'.$app;

    // upgrade config.php
    _upgrade_1_0_config_php($app_dir);

    // upgrade filters.yml
    _upgrade_1_0_filters_yml($app_dir);

    // upgrade all modules
    $dir = $app_dir.'/'.sfConfig::get('sf_app_module_dir_name');
    if ($dir)
    {
      // template dirs
      $template_dirs   = pakeFinder::type('directory')->name('templates')->mindepth(1)->maxdepth(1)->in($dir);
      $template_dirs[] = $app_dir.'/'.sfConfig::get('sf_app_template_dir_name');

      _upgrade_1_0_deprecated_for_templates($template_dirs);

      _upgrade_1_0_date_form_helpers($template_dirs);

      _upgrade_1_0_deprecated_for_generator($app_dir);

      _upgrade_1_0_cache_yml($app_dir);

      // actions dirs
      $action_dirs = pakeFinder::type('directory')->name('actions')->mindepth(1)->maxdepth(1)->in($dir);

      _upgrade_1_0_deprecated_for_actions($action_dirs);

      // view.yml
      _upgrade_1_0_view_yml($app_dir);

      _upgrade_1_0_php_files($app_dir);
    }
  }

  pake_echo_action('upgrade 1.0', 'done');

  pake_mkdirs(sfConfig::get('sf_root_dir').'/plugins');
  if (is_dir(sfConfig::get('sf_lib_dir').'/plugins'))
  {
    pake_echo_comment('WARNING: you must re-install all your plugins');
  }

  pake_echo_comment('Now, you must:');
  pake_echo_comment(' - rebuild your model classes: symfony propel-build-model');
  pake_echo_comment(' - clear the cache: symfony cc');
}

function _upgrade_1_0_i18n()
{
  $dirs = array(sfConfig::get('sf_lib_dir_name'), sfConfig::get('sf_apps_dir_name'));
  $finder = pakeFinder::type('file')->name('*.php');

  $seen = false;
  foreach ($finder->in($dirs) as $php_file)
  {
    $content = file_get_contents($php_file);

    $count = 0;
    $content = str_replace('sfConfig::get(\'sf_i18n_instance\')', 'sfContext::getInstance()->getI18N()', $content, $count);

    if ($count && !$seen)
    {
      $seen = true;
      pake_echo_comment('sfConfig::get(\'sf_i18n_instance\') is deprecated');
      pake_echo_comment(' use sfContext::getInstance()->getI18N()');
    }

    if ($count)
    {
      file_put_contents($php_file, $content);
    }
  }
}

function _upgrade_1_0_php_files($app_dir)
{
  pake_echo_action('upgrade 1.0', 'upgrading sf/ path configuration');

  $php_files = pakeFinder::type('file')->name('*.php')->in($app_dir);
  foreach ($php_files as $php_file)
  {
    $content = file_get_contents($php_file);

    $deprecated = array(
      "'/sf/js/prototype"     => "sfConfig::get('sf_prototype_web_dir').'/js",
      "'/sf/css/prototype"    => "sfConfig::get('sf_prototype_web_dir').'/css",
      "'/sf/js/sf_admin"      => "sfConfig::get('sf_admin_web_dir').'/js",
      "'/sf/css/sf_admin"     => "sfConfig::get('sf_admin_web_dir').'/css",
      "'/sf/images/sf_admin"  => "sfConfig::get('sf_admin_web_dir').'/images",
    );
    $seen = array();
    $updated = false;
    foreach ($deprecated as $old => $new)
    {
      $count = 0;
      $content = str_replace($old, $new, $content, $count);
      if ($count)
      {
        $updated = true;
      }
      if ($count && !isset($seen[$old]))
      {
        $seen[$old] = true;
        pake_echo_comment(sprintf('%s is deprecated', $old));
        pake_echo_comment(sprintf(' use %s', $new));
      }
    }

    if ($updated)
    {
      file_put_contents($php_file, $content);
    }
  }
}

function _upgrade_1_0_activate()
{
  pake_echo_action('upgrade 1.0', 'migrate activate to enabled');

  $config_files = array(
    'settings.yml' => array(
      'activated_modules:' => 'enabled_modules:  ',
    ),
    'cache.yml' => array(
      'activate:' => 'enabled: ',
    ),
    'logging.yml' => array(
      'active:' => 'enabled:',
    ),
    '*.php' => array(
      'sf_logging_'.'active' => 'sf_logging_enabled',
    ),
    'apps/*/modules/*/validate/*.yml' => array(
      'activate:' => 'enabled: ',
    ),
  );
  $seen = array();
  foreach ($config_files as $config_file => $changed)
  {
    list($dir, $config_file) = array(dirname($config_file), basename($config_file));
    $files = pakeFinder::type('file')->name($config_file)->in(sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.$dir);
    foreach ($files as $file)
    {
      $content = file_get_contents($file);

      $updated = false;
      foreach ($changed as $old => $new)
      {
        $content = str_replace($old, $new, $content, $count);
        if ($count)
        {
          $updated = true;
        }
        if ($count && !isset($seen[$config_file.$old]))
        {
          $seen[$config_file.$old] = true;

          pake_echo_comment(sprintf('%s is deprecated in %s', $old, $config_file));
          pake_echo_comment(sprintf(' use %s', $new));
        }
      }

      if ($updated)
      {
        file_put_contents($file, $content);
      }
    }
  }
}

function _upgrade_1_0_view_yml($app_dir)
{
  pake_echo_action('upgrade 1.0', 'upgrading view configuration');

  $yml_files = pakeFinder::type('file')->name('*.yml')->in($app_dir);
  foreach ($yml_files as $yml_file)
  {
    $content = file_get_contents($yml_file);

    $deprecated = array(
      '/sf/js/prototype'     => '%SF_PROTOTYPE_WEB_DIR%/js',
      '/sf/css/prototype'    => '%SF_PROTOTYPE_WEB_DIR%/css',
      '/sf/js/sf_admin'      => '%SF_ADMIN_WEB_DIR%/js',
      '/sf/css/sf_admin'     => '%SF_ADMIN_WEB_DIR%/css',
      '/sf/images/sf_admin'  => '%SF_ADMIN_WEB_DIR%/images',
    );
    $seen = array();
    $updated = false;
    foreach ($deprecated as $old => $new)
    {
      $count = 0;
      $content = str_replace($old, $new, $content, $count);
      if ($count)
      {
        $updated = true;
      }
      if ($count && !isset($seen[$old]))
      {
        $seen[$old] = true;
        pake_echo_comment(sprintf('%s is deprecated', $old));
        pake_echo_comment(sprintf(' use %s', $new));
      }
    }

    if ($updated)
    {
      file_put_contents($yml_file, $content);
    }
  }
}

function _upgrade_1_0_cache_yml($app_dir)
{
  pake_echo_action('upgrade 1.0', 'upgrading cache configuration');

  $yml_files = pakeFinder::type('files')->name('cache.yml')->in($app_dir);

  $seen = false;
  foreach ($yml_files as $yml_file)
  {
    $content = file_get_contents($yml_file);

    $count = 0;
    $updated = false;
    $content = preg_replace_callback('/type\:(\s*)(.+)$/m', '_upgrade_1_0_cache_yml_callback', $content, -1, $count);
    if ($count)
    {
      $updated = true;
    }
    if ($count && !$seen)
    {
      $seen = true;
      pake_echo_comment('"type" has been removed in cache.yml');
      pake_echo_comment('  read the doc about "with_layout"');
    }

    if ($updated)
    {
      file_put_contents($yml_file, $content);
    }
  }
}

function _upgrade_1_0_cache_yml_callback($match)
{
  return 'with_layout:'.str_repeat(' ', max(1, strlen($match[1]) - 6)).(0 === strpos($match[2], 'page') ? 'true' : 'false');
}

function _upgrade_1_0_deprecated_for_generator($app_dir)
{
  pake_echo_action('upgrade 1.0', 'upgrading deprecated helpers in generator.yml');

  $yml_files = pakeFinder::type('files')->name('generator.yml')->in($app_dir);

  $seen = array();
  $deprecated_str = array(
    'admin_input_upload_tag' => 'admin_input_file_tag',
  );
  foreach ($yml_files as $yml_file)
  {
    $updated = false;
    foreach ($deprecated_str as $old => $new)
    {
      $content = file_get_contents($yml_file);

      $count = 0;
      $content = str_replace($old, $new, $content, $count);
      if ($count)
      {
        $updated = true;
      }
      if ($count && !isset($seen[$old]))
      {
        $seen[$old] = true;
        pake_echo_comment(sprintf('%s() has been removed', $old));
        pake_echo_comment(sprintf(' use %s()', $new));
      }
    }

    if ($updated)
    {
      file_put_contents($yml_file, $content);
    }
  }
}

function _upgrade_1_0_deprecated_for_actions($action_dirs)
{
  pake_echo_action('upgrade 1.0', 'upgrading deprecated methods in actions');

  $php_files = pakeFinder::type('file')->name('*.php')->in($action_dirs);
  foreach ($php_files as $php_file)
  {
    $content = file_get_contents($php_file);

    $deprecated = array(
      '$this->addHttpMeta'   => '$this->getContext()->getResponse()->addHttpMeta',
      '$this->addMeta'       => '$this->getContext()->getResponse()->addMeta',
      '$this->setTitle'      => '$this->getContext()->getResponse()->setTitle',
      '$this->addStylesheet' => '$this->getContext()->getResponse()->addStylesheet',
      '$this->addJavascript' => '$this->getContext()->getResponse()->addJavascript',
    );
    $seen = array();
    $updated = false;
    foreach ($deprecated as $old => $new)
    {
      $count = 0;
      $content = str_replace($old, $new, $content, $count);
      if ($count)
      {
        $updated = true;
      }
      if ($count && !isset($seen[$old]))
      {
        $seen[$old] = true;
        pake_echo_comment(sprintf('%s has been removed', $old));
        pake_echo_comment(sprintf(' use %s', $new));
      }
    }

    if ($updated)
    {
      file_put_contents($php_file, $content);
    }
  }
}

function _upgrade_1_0_date_form_helpers($template_dirs)
{
  pake_echo_action('upgrade 1.0', 'upgrading date form helpers');

  $helpers = array(
    'select_day_tag', 'select_month_tag', 'select_year_tag', 'select_date_tag', 'select_second_tag', 'select_minute_tag',
    'select_hour_tag', 'select_ampm_tag', 'select_time_tag', 'select_datetime_tag', 'select_number_tag', 'select_timezone_tag',
  );
  $regex = '/('.implode('|', $helpers).')/';

  $php_files = pakeFinder::type('file')->name('*.php')->in($template_dirs);
  $seen = false;
  foreach ($php_files as $php_file)
  {
    $updated = false;

    $content = file_get_contents($php_file);

    if (preg_match($regex, $content) && false === strpos($content, 'DateForm'))
    {
      $content = "<?php use_helper('DateForm') ?>\n\n".$content;

      $updated = true;
      if (!$seen)
      {
        $seen = true;

        pake_echo_comment('date form helpers has been moved to the DateForm helper group');
        pake_echo_comment(' add use_helper(\'DateForm\')');
      }
    }

    if ($updated)
    {
      file_put_contents($php_file, $content);
    }
  }
}

function _upgrade_1_0_deprecated_for_templates($template_dirs)
{
  pake_echo_action('upgrade 1.0', 'upgrading deprecated helpers');

  $php_files = pakeFinder::type('file')->name('*.php')->in($template_dirs);
  $seen = array();
  $deprecated_str = array(
    'use_helpers'                   => 'use_helper',
    'object_admin_input_upload_tag' => 'object_admin_input_file_tag',
    'input_upload_tag'              => 'input_file_tag',
    '$sf_last_module'               => '$sf_context->getModuleName()',
    '$sf_last_action'               => '$sf_context->getActionName()',
    '$sf_first_module'              => '$sf_context->getActionStack()->getFirstEntry()->getModuleName()',
    '$sf_first_action'              => '$sf_context->getActionStack()->getFirstEntry()->getActionName()',
  );
  foreach ($php_files as $php_file)
  {
    $content = file_get_contents($php_file);

    $updated = false;
    $count = 0;

    foreach ($deprecated_str as $old => $new)
    {
      $content = str_replace($old, $new, $content, $count);
      if ($count)
      {
        $updated = true;
      }
      if ($count && !isset($seen[$old]))
      {
        $seen[$old] = true;
        pake_echo_comment(sprintf('%s has been removed', $old));
        pake_echo_comment(sprintf(' use %s', $new));
      }
    }

    if ($updated)
    {
      file_put_contents($php_file, $content);
    }
  }
}

function _upgrade_1_0_config_php($app_dir)
{
  pake_echo_action('upgrade 1.0', 'upgrading config.php');

  pake_copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/app/app/config/config.php', $app_dir.DIRECTORY_SEPARATOR.sfConfig::get('sf_config_dir_name').DIRECTORY_SEPARATOR.'config.php');
}

function _upgrade_1_0_filters_yml($app_dir)
{
  pake_echo_action('upgrade 1.0', 'upgrading filters.yml');

  $configFile = $app_dir.DIRECTORY_SEPARATOR.sfConfig::get('sf_config_dir_name').DIRECTORY_SEPARATOR.'filters.yml';
  $content = file_get_contents($configFile);

  // default symfony filters
  $default = file_get_contents(sfConfig::get('sf_symfony_data_dir').'/skeleton/app/app/config/filters.yml');

  $placeholder = '# generally, you will want to insert your own filters here';

  // upgrade module filters.yml
  $seen = false;
  $yml_files = pakeFinder::type('file')->name('filters.yml')->in($app_dir.DIRECTORY_SEPARATOR.'modules');
  foreach ($yml_files as $yml_file)
  {
    $module_content = file_get_contents($yml_file);

    if (false === strpos($module_content, 'rendering:'))
    {
      $lb = (strpos($module_content, "\r\n") !== false) ? "\r\n" : "\n";

      $module_content = str_replace($placeholder, $placeholder.$lb.$content.$lb.$module_content, $default);

      file_put_contents($yml_file, $module_content);

      if (!$seen)
      {
        pake_echo_comment('filters.yml now contains core symfony filters');
      }

      $seen = true;
    }
  }

  // upgrade app filters.yml
  if (false === strpos($content, 'rendering:'))
  {
    $lb = (strpos($content, "\r\n") !== false) ? "\r\n" : "\n";
    $content = str_replace($placeholder, $placeholder.$lb.$content, $default);

    file_put_contents($configFile, $content);

    if (!$seen)
    {
      pake_echo_comment('filters.yml now contains core symfony filters');
    }
  }

  // upgrade project filters.yml
  $configFile = sfConfig::get('sf_config_dir').DIRECTORY_SEPARATOR.'filters.yml';
  if (is_readable($configFile))
  {
    $content = file_get_contents($configFile);
    if (false === strpos($content, 'rendering:'))
    {
      $lb = (strpos($content, "\r\n") !== false) ? "\r\n" : "\n";
      $content = str_replace($placeholder, $placeholder.$lb.$content, $default);

      file_put_contents($configFile, $content);

      if (!$seen)
      {
        pake_echo_comment('filters.yml now contains core symfony filters');
      }
    }
  }
}

function _upgrade_1_0_main_config_php()
{
  pake_echo_action('upgrade 1.0', 'upgrading main config.php');

  $content = file_get_contents(sfConfig::get('sf_root_dir').'/config/config.php');

  if (false === strpos($content, 'sf_symfony_lib_dir'))
  {
    pake_echo_comment('symfony lib and data dir are now configured in main config.php');

    $lib_dir = sfConfig::get('sf_symfony_lib_dir');
    $data_dir = sfConfig::get('sf_symfony_data_dir');
    if (is_link('lib/symfony') && is_link('data/symfony'))
    {
      $config = <<<EOF


\$sf_symfony_lib_dir  = dirname(__FILE__).'/../lib/symfony';
\$sf_symfony_data_dir = dirname(__FILE__).'/../data/symfony';

EOF;
    }
    else
    {
      $config = <<<EOF


\$sf_symfony_lib_dir  = '$lib_dir';
\$sf_symfony_data_dir = '$data_dir';

EOF;
    }

    $content = preg_replace('/^<\?php/s', '<?php'.$config, $content);

    file_put_contents(sfConfig::get('sf_root_dir').'/config/config.php', $content);
  }
}

function _upgrade_1_0_propel_model()
{
  pake_echo_action('upgrade 1.0', 'upgrading require in models');

  $seen = false;
  $php_files = pakeFinder::type('file')->name('*.php')->in(sfConfig::get('sf_lib_dir').'/model');
  foreach ($php_files as $php_file)
  {
    $content = file_get_contents($php_file);

    $count1 = 0;
    $count2 = 0;
    $updated = false;
    $content = str_replace('require_once \'model', 'require_once \'lib/model', $content, $count1);
    $content = str_replace('include_once \'model', 'include_once \'lib/model', $content, $count2);
    if ($count1 || $count2)
    {
      $updated = true;
    }
    if (($count1 || $count2) && !$seen)
    {
      $seen = true;
      pake_echo_comment('model require must be lib/model/...');
      pake_echo_comment('  instead of model/...');
    }

    if ($updated)
    {
      file_put_contents($php_file, $content);
    }
  }
}

function _upgrade_1_0_schemas()
{
  pake_echo_action('upgrade 1.0', 'upgrading schemas');

  $seen = false;
  $xml_files = pakeFinder::type('file')->name('*schema.xml')->in(sfConfig::get('sf_config_dir'));
  foreach ($xml_files as $xml_file)
  {
    $content = file_get_contents($xml_file);

    if (preg_match('/<database[^>]*package[^>]*>/', $content))
    {
      continue;
    }

    $count = 0;
    $updated = false;
    $content = str_replace('<database', '<database package="lib.model"', $content, $count);
    if ($count)
    {
      $updated = true;
    }
    if ($count && !$seen)
    {
      $seen = true;
      pake_echo_comment('schema.xml must now have a database package');
      pake_echo_comment('  default is package="lib.model"');
    }

    if ($updated)
    {
      file_put_contents($xml_file, $content);
    }
  }
}

function _upgrade_1_0_propel_ini()
{
  pake_echo_action('upgrade 1.0', 'upgrading propel.ini configuration file');

  $propel_file = sfConfig::get('sf_config_dir').DIRECTORY_SEPARATOR.'propel.ini';

  if (is_readable($propel_file))
  {
    $updated = false;
    $propel_ini = file_get_contents($propel_file);

    $count = 0;

    // new target package (needed for new plugin system)
    $propel_ini = preg_replace('#propel\.targetPackage(\s*)=(\s*)model#', 'propel.targetPackage$1=$2lib.model', $propel_ini, -1, $count);
    if ($count)
    {
      $updated = true;
    }
    $propel_ini = preg_replace('#propel.php.dir(\s*)=(\s*)\${propel.output.dir}/lib#', 'propel.php.dir$1=$2\${propel.output.dir}', $propel_ini, -1, $count);
    if ($count)
    {
      $updated = true;
    }

    if (false === strpos($propel_ini, 'propel.packageObjectModel'))
    {
      $updated = true;
      $propel_ini = rtrim($propel_ini);
      $propel_ini .= "\npropel.packageObjectModel = true\n";
    }

    // new propel builder class to be able to remove require_* and strip comments
    $propel_ini = str_replace('propel.engine.builder.om.php5.PHP5ExtensionObjectBuilder', 'addon.propel.builder.SfExtensionObjectBuilder', $propel_ini, $count);
    if ($count)
    {
      $updated = true;
    }
    $propel_ini = str_replace('propel.engine.builder.om.php5.PHP5ExtensionPeerBuilder', 'addon.propel.builder.SfExtensionPeerBuilder', $propel_ini, $count);
    if ($count)
    {
      $updated = true;
    }
    $propel_ini = str_replace('propel.engine.builder.om.php5.PHP5MultiExtendObjectBuilder', 'addon.propel.builder.SfMultiExtendObjectBuilder', $propel_ini, $count);
    if ($count)
    {
      $updated = true;
    }
    $propel_ini = str_replace('propel.engine.builder.om.php5.PHP5MapBuilderBuilder', 'addon.propel.builder.SfMapBuilderBuilder', $propel_ini, $count);
    if ($count)
    {
      $updated = true;
    }

    // replace old symfony.addon.propel path to addon.propel
    $propel_ini = str_replace('symfony.addon.propel.builder.', 'addon.propel.builder.', $propel_ini, $count);
    if ($count)
    {
      $updated = true;
    }

    if (false === strpos($propel_ini, 'addIncludes'))
    {
      $updated = true;
      $propel_ini .= <<<EOF

propel.builder.addIncludes  = false
propel.builder.addComments  = false
propel.builder.addBehaviors = false

EOF;

      pake_echo_comment('there are 3 new propel.ini options:');
      pake_echo_comment(' - propel.builder.addIncludes');
      pake_echo_comment(' - propel.builder.addComments');
      pake_echo_comment(' - propel.builder.addBehaviors');
    }

    if ($updated)
    {
      file_put_contents($propel_file, $propel_ini);
    }
  }
}

function _add_1_0_test_bootstraps()
{
  pake_echo_action('upgrade 1.0', 'add test bootstrap files');

  pake_mkdirs(sfConfig::get('sf_root_dir').'/test/bootstrap');

  pake_copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/project/test/bootstrap/functional.php', sfConfig::get('sf_root_dir').'/test/bootstrap/functional.php');
  pake_copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/project/test/bootstrap/unit.php', sfConfig::get('sf_root_dir').'/test/bootstrap/unit.php');
}
