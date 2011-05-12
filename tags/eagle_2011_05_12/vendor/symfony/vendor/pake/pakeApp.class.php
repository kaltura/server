<?php

/**
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeApp.class.php 2574 2006-10-31 06:44:28Z fabien $
 */

/**
 *
 * main pake class.
 *
 * This class is a singleton.
 *
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeApp.class.php 2574 2006-10-31 06:44:28Z fabien $
 */
class pakeApp
{
  const VERSION = '1.1.DEV';

  private static $MAX_LINE_SIZE = 65;
  private static $PROPERTIES = array();
  private static $PAKEFILES = array('pakefile', 'Pakefile', 'pakefile.php', 'Pakefile.php');
  private static $PLUGINDIRS = array();
  private static $OPTIONS = array(
    array('--dry-run',  '-n', pakeGetopt::NO_ARGUMENT,       "Do a dry run without executing actions."),
    array('--help',     '-H', pakeGetopt::NO_ARGUMENT,       "Display this help message."),
    array('--libdir',   '-I', pakeGetopt::REQUIRED_ARGUMENT, "Include LIBDIR in the search path for required modules."),
    array('--nosearch', '-N', pakeGetopt::NO_ARGUMENT,       "Do not search parent directories for the pakefile."),
    array('--prereqs',  '-P', pakeGetopt::NO_ARGUMENT,       "Display the tasks and dependencies, then exit."),
    array('--quiet',    '-q', pakeGetopt::NO_ARGUMENT,       "Do not log messages to standard output."),
    array('--pakefile', '-f', pakeGetopt::REQUIRED_ARGUMENT, "Use FILE as the pakefile."),
    array('--require',  '-r', pakeGetopt::REQUIRED_ARGUMENT, "Require MODULE before executing pakefile."),
    array('--tasks',    '-T', pakeGetopt::NO_ARGUMENT,       "Display the tasks and dependencies, then exit."),
    array('--trace',    '-t', pakeGetopt::NO_ARGUMENT,       "Turn on invoke/execute tracing, enable full backtrace."),
    array('--usage',    '-h', pakeGetopt::NO_ARGUMENT,       "Display usage."),
    array('--verbose',  '-v', pakeGetopt::NO_ARGUMENT,       "Log message to standard output (default)."),
    array('--version',  '-V', pakeGetopt::NO_ARGUMENT,       "Display the program version."),
  );

  private $opt = null;
  private $nosearch = false;
  private $trace = false;
  private $verbose = true;
  private $dryrun = false;
  private $nowrite = false;
  private $show_tasks = false;
  private $show_prereqs = false;
  private $pakefile = '';
  private static $instance = null;

  private function __construct()
  {
    self::$PLUGINDIRS[] = dirname(__FILE__).'/tasks';
  }

  public static function get_plugin_dirs()
  {
    return self::$PLUGINDIRS;
  }

  public function get_properties()
  {
    return self::$PROPERTIES;
  }

  public function set_properties($properties)
  {
    self::$PROPERTIES = $properties;
  }

  public static function get_instance()
  {
    if (!self::$instance) self::$instance = new pakeApp();

    return self::$instance;
  }

  public function get_verbose()
  {
    return $this->verbose;
  }

  public function get_trace()
  {
    return $this->trace;
  }

  public function get_dryrun()
  {
    return $this->dryrun;
  }

  public function run($pakefile = null, $options = null, $load_pakefile = true)
  {
    if ($pakefile)
    {
      pakeApp::$PAKEFILES = array($pakefile);
    }

    $this->handle_options($options);
    if ($load_pakefile)
    {
      $this->load_pakefile();
    }

    if ($this->show_tasks)
    {
      $this->display_tasks_and_comments();
    }
    else if ($this->show_prereqs)
    {
      $this->display_prerequisites();
    }
    else
    {
      $args = $this->opt->get_arguments();
      $task = array_shift($args);

      $abbrev_options = $this->abbrev(array_keys(pakeTask::get_tasks()));
      $task = pakeTask::get_full_task_name($task);
      if (!$task)
      {
        $task = 'default';
      }

      if (!array_key_exists($task, $abbrev_options))
      {
        throw new pakeException(sprintf('Task "%s" is not defined.', $task));
      }
      else if (count($abbrev_options[$task]) > 1)
      {
        throw new pakeException(sprintf('Task "%s" is ambiguous (%s).', $task, implode(', ', $abbrev_options[$task])));
      }
      else
      {
        return pakeTask::get($abbrev_options[$task][0])->invoke($args);
      }
    }
  }

  // Read and handle the command line options.
  public function handle_options($options = null)
  {
    $this->opt = new pakeGetopt(pakeApp::$OPTIONS);
    $this->opt->parse($options);
    foreach ($this->opt->get_options() as $opt => $value)
    {
      $this->do_option($opt, $value);
    }
  }

  // True if one of the files in RAKEFILES is in the current directory.
  // If a match is found, it is copied into @pakefile.
  public function have_pakefile()
  {
    foreach (pakeApp::$PAKEFILES as $file)
    {
      if (file_exists($file))
      {
        $this->pakefile = $file;
        return true;
      }
    }

    return false;
  }

  public function load_pakefile()
  {
    $here = getcwd();
    while (!$this->have_pakefile())
    {
      chdir('..');
      if (getcwd() == $here || $this->nosearch)
      {
        throw new pakeException(sprintf('No pakefile found (looking for: %s)', join(', ', pakeApp::$PAKEFILES))."\n");
      }

      $here = getcwd();
    }

    require_once($this->pakefile);
  }

  // Do the option defined by +opt+ and +value+.
  public function do_option($opt, $value)
  {
    switch ($opt)
    {
      case 'dry-run':
        $this->verbose = true;
        $this->nowrite = true;
        $this->dryrun = true;
        $this->trace = true;
        break;
      case 'help':
        $this->help();
        exit();
      case 'libdir':
        set_include_path($value.PATH_SEPARATOR.get_include_path());
        break;
      case 'nosearch':
        $this->nosearch = true;
        break;
      case 'prereqs':
        $this->show_prereqs = true;
        break;
      case 'quiet':
        $this->verbose = false;
        break;
      case 'pakefile':
        pakeApp::$PAKEFILES = array($value);
        break;
      case 'require':
        require $value;
        break;
      case 'tasks':
        $this->show_tasks = true;
        break;
      case 'trace':
        $this->trace = true;
        $this->verbose = true;
        break;
      case 'usage':
        $this->usage();
        exit();
      case 'verbose':
        $this->verbose = true;
        break;
      case 'version':
        echo sprintf('pake version %s', pakeColor::colorize(pakeApp::VERSION, 'INFO'))."\n";
        exit();
      default:
        throw new pakeException(sprintf("Unknown option: %s", $opt));
    }
  }

  // Display the program usage line.
  public function usage()
  {
    echo "pake [-f pakefile] {options} targets...\n".pakeColor::colorize("Try pake -H for more information", 'INFO')."\n";
  }

  // Display the rake command line help.
  public function help()
  {
    $this->usage();
    echo "\n";
    echo "available options:";
    echo "\n";

    foreach (pakeApp::$OPTIONS as $option)
    {
      list($long, $short, $mode, $comment) = $option;
      if ($mode == pakeGetopt::REQUIRED_ARGUMENT)
      {
        if (preg_match('/\b([A-Z]{2,})\b/', $comment, $match))
          $long .= '='.$match[1];
      }
      printf("  %-20s (%s)\n", pakeColor::colorize($long, 'INFO'), pakeColor::colorize($short, 'INFO'));
      printf("      %s\n", $comment);
    }
  }

  // Display the tasks and dependencies.
  public function display_tasks_and_comments()
  {
    $width = 0;
    $tasks = pakeTask::get_tasks();
    foreach ($tasks as $name => $task)
    {
      $w = strlen(pakeTask::get_mini_task_name($name));
      if ($w > $width) $width = $w;
    }
    $width += strlen(pakeColor::colorize(' ', 'INFO'));

    echo "available pake tasks:\n";

    // display tasks
    $has_alias = false;
    ksort($tasks);
    foreach ($tasks as $name => $task)
    {
      if ($task->get_alias())
      {
        $has_alias = true;
      }

      if (!$task->get_alias() && $task->get_comment())
      {
        $mini_name = pakeTask::get_mini_task_name($name);
        printf('  %-'.$width.'s > %s'."\n", pakeColor::colorize($mini_name, 'INFO'), $task->get_comment().($mini_name != $name ? ' ['.$name.']' : ''));
      }
    }

    if ($has_alias)
    {
      print("\ntask aliases:\n");

      // display aliases
      foreach ($tasks as $name => $task)
      {
        if ($task->get_alias())
        {
          $mini_name = pakeTask::get_mini_task_name($name);
          printf('  %-'.$width.'s = pake %s'."\n", pakeColor::colorize(pakeTask::get_mini_task_name($name), 'INFO'), $task->get_alias().($mini_name != $name ? ' ['.$name.']' : ''));
        }
      }
    }
  }

  // Display the tasks and prerequisites
  public function display_prerequisites()
  {
    foreach (pakeTask::get_tasks() as $name => $task)
    {
      echo "pake ".pakeTask::get_mini_task_name($name)."\n";
      foreach ($task->get_prerequisites() as $prerequisite)
      {
        echo "    $prerequisite\n";
      }
    }
  }

  public static function get_files_from_argument($arg, $target_dir = '', $relative = false)
  {
    $files = array();
    if (is_array($arg))
    {
      $files = $arg;
    }
    else if (is_string($arg))
    {
      $files[] = $arg;
    }
    else if ($arg instanceof pakeFinder)
    {
      $files = $arg->in($target_dir);
    }
    else
    {
      throw new pakeException('Wrong argument type (must be a list, a string or a pakeFinder object).');
    }

    if ($relative && $target_dir)
    {
      $files = preg_replace('/^'.preg_quote(realpath($target_dir), '/').'/', '', $files);

      // remove leading /
      $files = array_map(create_function('$f', 'return 0 === strpos($f, DIRECTORY_SEPARATOR) ? substr($f, 1) : $f;'), $files);
    }

    return $files;
  }

  public static function excerpt($text, $size = null)
  {
    if (!$size)
    {
      $size = self::$MAX_LINE_SIZE;
    }

    if (strlen($text) < $size)
    {
      return $text;
    }

    $subsize = floor(($size - 3) / 2);

    return substr($text, 0, $subsize).pakeColor::colorize('...', 'INFO').substr($text, -$subsize);
  }

  /* see perl Text::Abbrev module */
  private function abbrev($options)
  {
    $abbrevs = array();
    $table = array();

    foreach ($options as $option)
    {
      $option = pakeTask::get_mini_task_name($option);

      for ($len = (strlen($option)) - 1; $len > 0; --$len)
      {
        $abbrev = substr($option, 0, $len);
        if (!array_key_exists($abbrev, $table))
          $table[$abbrev] = 1;
        else
          ++$table[$abbrev];

        $seen = $table[$abbrev];
        if ($seen == 1)
        { 
          // we're the first word so far to have this abbreviation.
          $abbrevs[$abbrev] = array($option);
        }
        else if ($seen == 2)
        { 
          // we're the second word to have this abbreviation, so we can't use it.
          //unset($abbrevs[$abbrev]);
          $abbrevs[$abbrev][] = $option;
        }
        else
        { 
          // we're the third word to have this abbreviation, so skip to the next word.
          continue;
        }
      }
    }

    // Non-abbreviations always get entered, even if they aren't unique
    foreach ($options as $option)
    {
      $abbrevs[$option] = array($option);
    }

    return $abbrevs;
  }
}
