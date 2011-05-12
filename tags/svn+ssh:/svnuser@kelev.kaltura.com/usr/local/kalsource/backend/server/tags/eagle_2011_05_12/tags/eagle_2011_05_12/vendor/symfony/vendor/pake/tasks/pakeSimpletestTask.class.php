<?php

/**
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeSimpletestTask.class.php 1791 2006-08-23 21:17:06Z fabien $
 */

class pakeSimpletestTask
{
  public static function import_default_tasks()
  {
    pake_desc('launch project test suite');
    pake_task('pakeSimpletestTask::test');
  }

  public static function call_simpletest($task, $type = 'text', $dirs = array())
  {
    // remove E_STRICT because simpletest is not E_STRICT compatible
    $old_error_reporting = ini_get('error_reporting');
    if ($old_error_reporting & E_STRICT)
    {
      error_reporting($old_error_reporting ^ E_STRICT);
    }

    set_include_path('test'.PATH_SEPARATOR.'lib'.PATH_SEPARATOR.'classes'.PATH_SEPARATOR.get_include_path());

    include_once('simpletest/unit_tester.php');
    include_once('simpletest/web_tester.php');
    if (!class_exists('GroupTest'))
    {
      throw new pakeException('You must install SimpleTest to use this task.');
    }

    require_once('simpletest/reporter.php');
    require_once('simpletest/mock_objects.php');

    $base_test_dir = 'test';
    $test_dirs = array();

    // run tests only in these subdirectories
    if ($dirs)
    {
      foreach ($dirs as $dir)
      {
        $test_dirs[] = $base_test_dir.DIRECTORY_SEPARATOR.$dir;
      }
    }
    else
    {
      $test_dirs[] = $base_test_dir;
    }

    $test = new GroupTest('Test suite in ('.implode(', ', $test_dirs).')');
    $files = pakeFinder::type('file')->name('*Test.php')->in($test_dirs);
    foreach ($files as $file)
    {
      $test->addTestFile($file);
    }

    if (count($files))
    {
      ob_start();
      if ($type == 'html')
      {
        $result = $test->run(new HtmlReporter());
      }
      else if ($type == 'xml')
      {
        $result = $test->run(new XmlReporter());
      }
      else
      {
        $result = $test->run(new TextReporter());
      }
      $content = ob_get_contents();
      ob_end_clean();

      if ($task->is_verbose())
      {
        echo $content;
      }
    }
    else
    {
      throw new pakeException('No test to run.');
    }

    error_reporting($old_error_reporting);
  }

  public static function run_test($task, $args)
  {
    $types = array('text', 'html', 'xml');
    $type = 'text';
    if (array_key_exists(0, $args) && in_array($args[0], $types))
    {
      $type = $args[0];
      array_shift($args);
    }

    $dirs = array();
    if (is_array($args) && array_key_exists(0, $args))
    {
      $dirs[] = $args[0];
    }

    self::call_simpletest($task, $type, $dirs);
  }
}
