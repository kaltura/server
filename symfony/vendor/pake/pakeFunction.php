<?php

/**
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeFunction.php 3263 2007-01-13 14:20:52Z fabien $
 */

require_once dirname(__FILE__).'/pakeException.class.php';
require_once dirname(__FILE__).'/pakeYaml.class.php';
require_once dirname(__FILE__).'/pakeGetopt.class.php';
require_once dirname(__FILE__).'/pakeFinder.class.php';
require_once dirname(__FILE__).'/pakeTask.class.php';
require_once dirname(__FILE__).'/pakeFileTask.class.php';
require_once dirname(__FILE__).'/pakeColor.class.php';
require_once dirname(__FILE__).'/pakeApp.class.php';

function pake_import($name, $import_default_tasks = true)
{
  $class_name = 'pake'.ucfirst(strtolower($name)).'Task';

  if (!class_exists($class_name))
  {
    // plugin available?
    $plugin_path = '';
    foreach (pakeApp::get_plugin_dirs() as $dir)
    {
      if (file_exists($dir.DIRECTORY_SEPARATOR.$class_name.'.class.php'))
      {
        $plugin_path = $dir.DIRECTORY_SEPARATOR.$class_name.'.class.php';
        break;
      }
    }

    if ($plugin_path)
    {
      require_once $plugin_path;
    }
    else
    {
      throw new pakeException(sprintf('Plugin "%s" does not exist.', $name));
    }
  }

  if ($import_default_tasks && is_callable($class_name, 'import_default_tasks'))
  {
    call_user_func(array($class_name, 'import_default_tasks'));
  }
}

function pake_task($name)
{
  $args = func_get_args();
  array_shift($args);
  pakeTask::define_task($name, $args);

  return $name;
}

function pake_alias($alias, $name)
{
  pakeTask::define_alias($alias, $name);

  return $alias;
}

function pake_desc($comment)
{
  pakeTask::define_comment($comment);
}

function pake_properties($property_file)
{
  $file = $property_file;
  if (!pakeFinder::isPathAbsolute($file))
  {
    $file = getcwd().DIRECTORY_SEPARATOR.$property_file;
  }

  if (file_exists($file))
  {
    pakeApp::get_instance()->set_properties(parse_ini_file($file, true));
  }
  else
  {
    throw new pakeException('Properties file does not exist.');
  }
}

function pake_file($name)
{
  $args = func_get_args();
  array_shift($args);
  pakeFileTask::define_task($name, $args);

  return $name;
}

function pake_mkdirs($path, $mode = 0777)
{
  if (is_dir($path))
  {
    return true;
  }

  pake_echo_action('dir+', $path);

  return @mkdir($path, $mode, true);
}

/*
  override => boolean
*/
function pake_copy($origin_file, $target_file, $options = array())
{
  if (!array_key_exists('override', $options))
  {
    $options['override'] = false;
  }

  // we create target_dir if needed
  if (!is_dir(dirname($target_file)))
  {
    pake_mkdirs(dirname($target_file));
  }

  $most_recent = false;
  if (file_exists($target_file))
  {
    $stat_target = stat($target_file);
    $stat_origin = stat($origin_file);
    $most_recent = ($stat_origin['mtime'] > $stat_target['mtime']) ? true : false;
  }

  if ($options['override'] || !file_exists($target_file) || $most_recent)
  {
    pake_echo_action('file+', $target_file);
    copy($origin_file, $target_file);
  }
}

function pake_rename($origin, $target, $options = array())
{
  // we check that target does not exist
  if (is_readable($target))
  {
    throw new pakeException(sprintf('Cannot rename because the target "%" already exist.', $target));
  }

  pake_echo_action('rename', $origin.' > '.$target);
  rename($origin, $target);
}

function pake_mirror($arg, $origin_dir, $target_dir, $options = array())
{
  $files = pakeApp::get_files_from_argument($arg, $origin_dir, true);

  foreach ($files as $file)
  {
    if (is_dir($origin_dir.DIRECTORY_SEPARATOR.$file))
    {
      pake_mkdirs($target_dir.DIRECTORY_SEPARATOR.$file);
    }
    else if (is_file($origin_dir.DIRECTORY_SEPARATOR.$file))
    {
      pake_copy($origin_dir.DIRECTORY_SEPARATOR.$file, $target_dir.DIRECTORY_SEPARATOR.$file, $options);
    }
    else if (is_link($origin_dir.DIRECTORY_SEPARATOR.$file))
    {
      pake_symlink($origin_dir.DIRECTORY_SEPARATOR.$file, $target_dir.DIRECTORY_SEPARATOR.$file);
    }
    else
    {
      throw new pakeException(sprintf('Unable to determine "%s" type', $file));
    }
  }
}

function pake_remove($arg, $target_dir)
{
  $files = array_reverse(pakeApp::get_files_from_argument($arg, $target_dir));

  foreach ($files as $file)
  {
    if (is_dir($file) && !is_link($file))
    {
      pake_echo_action('dir-', $file);

      rmdir($file);
    }
    else
    {
      pake_echo_action(is_link($file) ? 'link-' : 'file-', $file);

      unlink($file);
    }
  }
}

function pake_touch($arg, $target_dir)
{
  $files = pakeApp::get_files_from_argument($arg, $target_dir);

  foreach ($files as $file)
  {
    pake_echo_action('file+', $file);

    touch($file);
  }
}

function pake_replace_tokens($arg, $target_dir, $begin_token, $end_token, $tokens)
{
  $files = pakeApp::get_files_from_argument($arg, $target_dir, true);

  foreach ($files as $file)
  {
    $replaced = false;
    $content = file_get_contents($target_dir.DIRECTORY_SEPARATOR.$file);
    foreach ($tokens as $key => $value)
    {
      $content = str_replace($begin_token.$key.$end_token, $value, $content, $count);
      if ($count) $replaced = true;
    }

    pake_echo_action('tokens', $target_dir.DIRECTORY_SEPARATOR.$file);

    file_put_contents($target_dir.DIRECTORY_SEPARATOR.$file, $content);
  }
}

function pake_symlink($origin_dir, $target_dir, $copy_on_windows = false)
{
  if (!function_exists('symlink') && $copy_on_windows)
  {
    $finder = pakeFinder::type('any')->ignore_version_control();
    pake_mirror($finder, $origin_dir, $target_dir);
    return;
  }

  $ok = false;
  if (is_link($target_dir))
  {
    if (readlink($target_dir) != $origin_dir)
    {
      unlink($target_dir);
    }
    else
    {
      $ok = true;
    }
  }

  if (!$ok)
  {
    pake_echo_action('link+', $target_dir);
    symlink($origin_dir, $target_dir);
  }
}

function pake_chmod($arg, $target_dir, $mode, $umask = 0000)
{
  $current_umask = umask();
  umask($umask);

  $files = pakeApp::get_files_from_argument($arg, $target_dir, true);

  foreach ($files as $file)
  {
    pake_echo_action(sprintf('chmod %o', $mode), $target_dir.DIRECTORY_SEPARATOR.$file);
    chmod($target_dir.DIRECTORY_SEPARATOR.$file, $mode);
  }

  umask($current_umask);
}

function pake_sh($cmd)
{
  $verbose = pakeApp::get_instance()->get_verbose();
  pake_echo_action('exec ', $cmd);

  ob_start();
  passthru($cmd.' 2>&1', $return);
  $content = ob_get_contents();
  ob_end_clean();

  if ($return > 0)
  {
    throw new pakeException(sprintf('Problem executing command %s', $verbose ? "\n".$content : ''));
  }

  return $content;
}

function pake_strip_php_comments($arg)
{
  /* T_ML_COMMENT does not exist in PHP 5.
   * The following three lines define it in order to
   * preserve backwards compatibility.
   *
   * The next two lines define the PHP 5-only T_DOC_COMMENT,
   * which we will mask as T_ML_COMMENT for PHP 4.
   */
  if (!defined('T_ML_COMMENT'))
  {
    define('T_ML_COMMENT', T_COMMENT);
  }
  else
  {
    if (!defined('T_DOC_COMMENT')) define('T_DOC_COMMENT', T_ML_COMMENT);
  }

  $files = pakeApp::get_files_from_argument($arg);

  foreach ($files as $file)
  {
    if (!is_file($file)) continue;

    $source = file_get_contents($file);
    $output = '';

    $tokens = token_get_all($source);
    foreach ($tokens as $token)
    {
      if (is_string($token))
      {
        // simple 1-character token
        $output .= $token;
      }
      else
      {
        // token array
        list($id, $text) = $token;
        switch ($id)
        {
          case T_COMMENT:
          case T_ML_COMMENT: // we've defined this
          case T_DOC_COMMENT: // and this
            // no action on comments
            break;
          default:
          // anything else -> output "as is"
          $output .= $text;
          break;
        }
      }
    }

    file_put_contents($file, $output);
  }
}

function pake_format_action($section, $text, $size = null)
{
  if (pakeApp::get_instance()->get_verbose())
  {
    $width = 9 + strlen(pakeColor::colorize('', 'INFO'));
    return sprintf('>> %-'.$width.'s %s', pakeColor::colorize($section, 'INFO'), pakeApp::excerpt($text, $size))."\n";
  }
}

function pake_echo_action($section, $text)
{
  echo pake_format_action($section, $text);
}

function pake_excerpt($text)
{
  if (pakeApp::get_instance()->get_verbose())
  {
    echo pakeApp::excerpt($text)."\n";
  }
}

function pake_echo($text)
{
  if (pakeApp::get_instance()->get_verbose())
  {
    echo $text."\n";
  }
}

function pake_echo_comment($text)
{
  if (pakeApp::get_instance()->get_verbose())
  {
    echo sprintf(pakeColor::colorize('   # %s', 'COMMENT'), $text)."\n";
  }
}

// register our default exception handler
function pake_exception_default_handler($exception)
{
  $e = new pakeException();
  $e->render($exception);
  exit(1);
}
set_exception_handler('pake_exception_default_handler');

// fix php behavior if using cgi php
// from http://www.sitepoint.com/article/php-command-line-1/3
if (false !== strpos(PHP_SAPI, 'cgi'))
{
   // handle output buffering
   @ob_end_flush();
   ob_implicit_flush(true);

   // PHP ini settings
   set_time_limit(0);
   ini_set('track_errors', true);
   ini_set('html_errors', false);
   ini_set('magic_quotes_runtime', false);

   // define stream constants
   define('STDIN', fopen('php://stdin', 'r'));
   define('STDOUT', fopen('php://stdout', 'w'));
   define('STDERR', fopen('php://stderr', 'w'));

   // change directory
   if (isset($_SERVER['PWD']))
   {
     chdir($_SERVER['PWD']);
   }

   // close the streams on script termination
   register_shutdown_function(create_function('', 'fclose(STDIN); fclose(STDOUT); fclose(STDERR); return true;'));
}
