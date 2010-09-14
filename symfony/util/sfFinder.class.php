<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFinder.class.php 3268 2007-01-13 20:19:33Z fabien $
 */

/**
 *
 * Allow to build rules to find files and directories.
 *
 * All rules may be invoked several times, except for ->in() method.
 * Some rules are cumulative (->name() for example) whereas others are destructive
 * (most recent value is used, ->maxdepth() method for example).
 *
 * All methods return the current sfFinder object to allow easy chaining:
 *
 * $files = sfFinder::type('file')->name('*.php')->in(.);
 *
 * Interface loosely based on perl File::Find::Rule module.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFinder.class.php 3268 2007-01-13 20:19:33Z fabien $
 */
class sfFinder
{
  protected $type        = 'file';
  protected $names       = array();
  protected $prunes      = array();
  protected $discards    = array();
  protected $execs       = array();
  protected $mindepth    = 0;
  protected $sizes       = array();
  protected $maxdepth    = 1000000;
  protected $relative    = false;
  protected $follow_link = false;

  /**
   * Sets maximum directory depth.
   *
   * Finder will descend at most $level levels of directories below the starting point.
   *
   * @param  integer level
   * @return object current sfFinder object
   */
  public function maxdepth($level)
  {
    $this->maxdepth = $level;

    return $this;
  }

  /**
   * Sets minimum directory depth.
   *
   * Finder will start applying tests at level $level.
   *
   * @param  integer level
   * @return object current sfFinder object
   */
  public function mindepth($level)
  {
    $this->mindepth = $level;

    return $this;
  }

  public function get_type()
  {
    return $this->type;
  }

  /**
   * Sets the type of elements to returns.
   *
   * @param  string directory or file or any (for both file and directory)
   * @return object new sfFinder object
   */
  public static function type($name)
  {
    $finder = new sfFinder();

    if (strtolower(substr($name, 0, 3)) == 'dir')
    {
      $finder->type = 'directory';
    }
    else if (strtolower($name) == 'any')
    {
      $finder->type = 'any';
    }
    else
    {
      $finder->type = 'file';
    }

    return $finder;
  }

  /*
   * glob, patterns (must be //) or strings
   */
  protected function to_regex($str)
  {
    if ($str{0} == '/' && $str{strlen($str) - 1} == '/')
    {
      return $str;
    }
    else
    {
      return sfGlobToRegex::glob_to_regex($str);
    }
  }

  protected function args_to_array($arg_list, $not = false)
  {
    $list = array();

    for ($i = 0; $i < count($arg_list); $i++)
    {
      if (is_array($arg_list[$i]))
      {
        foreach ($arg_list[$i] as $arg)
        {
          $list[] = array($not, $this->to_regex($arg));
        }
      }
      else
      {
        $list[] = array($not, $this->to_regex($arg_list[$i]));
      }
    }

    return $list;
  }

  /**
   * Adds rules that files must match.
   *
   * You can use patterns (delimited with / sign), globs or simple strings.
   *
   * $finder->name('*.php')
   * $finder->name('/\.php$/') // same as above
   * $finder->name('test.php')
   *
   * @param  list   a list of patterns, globs or strings
   * @return object current sfFinder object
   */
  public function name()
  {
    $args = func_get_args();
    $this->names = array_merge($this->names, $this->args_to_array($args));

    return $this;
  }

  /**
   * Adds rules that files must not match.
   *
   * @see    ->name()
   * @param  list   a list of patterns, globs or strings
   * @return object current sfFinder object
   */
  public function not_name()
  {
    $args = func_get_args();
    $this->names = array_merge($this->names, $this->args_to_array($args, true));

    return $this;
  }

  /**
   * Adds tests for file sizes.
   *
   * $finder->size('> 10K');
   * $finder->size('<= 1Ki');
   * $finder->size(4);
   *
   * @param  list   a list of comparison strings
   * @return object current sfFinder object
   */
  public function size()
  {
    $args = func_get_args();
    for ($i = 0; $i < count($args); $i++)
    {
      $this->sizes[] = new sfNumberCompare($args[$i]);
    }

    return $this;
  }

  /**
   * Traverses no further.
   *
   * @param  list   a list of patterns, globs to match
   * @return object current sfFinder object
   */
  public function prune()
  {
    $args = func_get_args();
    $this->prunes = array_merge($this->prunes, $this->args_to_array($args));

    return $this;
  }

  /**
   * Discards elements that matches.
   *
   * @param  list   a list of patterns, globs to match
   * @return object current sfFinder object
   */
  public function discard()
  {
    $args = func_get_args();
    $this->discards = array_merge($this->discards, $this->args_to_array($args));

    return $this;
  }

  /**
   * Ignores version control directories.
   *
   * Currently supports subversion, CVS, DARCS, Gnu Arch, Monotone, Bazaar-NG
   *
   * @return object current pakeFinder object
   */
  public function ignore_version_control()
  {
    $ignores = array('.svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr');

    return $this->discard($ignores)->prune($ignores);
  }

  /**
   * Executes function or method for each element.
   *
   * Element match if functino or method returns true.
   *
   * $finder->exec('myfunction');
   * $finder->exec(array($object, 'mymethod'));
   *
   * @param  mixed  function or method to call
   * @return object current sfFinder object
   */
  public function exec()
  {
    $args = func_get_args();
    for ($i = 0; $i < count($args); $i++)
    {
      if (is_array($args[$i]) && !method_exists($args[$i][0], $args[$i][1]))
      {
        throw new sfException("method {$args[$i][1]} does not exist for object {$args[$i][0]}");
      }
      else if (!is_array($args[$i]) && !function_exists($args[$i]))
      {
        throw new sfException("function {$args[$i]} does not exist");
      }

      $this->execs[] = $args[$i];
    }

    return $this;
  }

  /**
   * Returns relative paths for all files and directories.
   *
   * @return object current sfFinder object
   */
  public function relative()
  {
    $this->relative = true;

    return $this;
  }

  /**
   * Symlink following.
   *
   * @return object current sfFinder object
   */
  public function follow_link()
  {
    $this->follow_link = true;

    return $this;
  }

  /**
   * Searches files and directories which match defined rules.
   *
   * @return array list of files and directories
   */
  public function in()
  {
    $files    = array();
    $here_dir = getcwd();
    $numargs  = func_num_args();
    $arg_list = func_get_args(); 

    // first argument is an array?
    if ($numargs == 1 && is_array($arg_list[0]))
    {
      $arg_list = $arg_list[0];
      $numargs  = count($arg_list);
    }

    for ($i = 0; $i < $numargs; $i++)
    {
      $real_dir = realpath($arg_list[$i]);

      // absolute path?
      if (!self::isPathAbsolute($real_dir))
      {
        $dir = $here_dir.DIRECTORY_SEPARATOR.$real_dir;
      }
      else
      {
        $dir = $real_dir;
      }

      if (!is_dir($real_dir))
      {
        continue;
      }

      if ($this->relative)
      {
        $files = array_merge($files, str_replace($dir.DIRECTORY_SEPARATOR, '', $this->search_in($dir)));
      }
      else
      {
        $files = array_merge($files, $this->search_in($dir));
      }
    }

    return array_unique($files);
  }

  protected function search_in($dir, $depth = 0)
  {
    if ($depth > $this->maxdepth)
    {
      return array();
    }

    if (is_link($dir) && !$this->follow_link)
    {
      return array();
    }

    $files = array();

    if (is_dir($dir))
    {
      $current_dir = opendir($dir);
      while (false !== $entryname = readdir($current_dir))
      {
        if ($entryname == '.' || $entryname == '..') continue;

        $current_entry = $dir.DIRECTORY_SEPARATOR.$entryname;
        if (is_link($current_entry) && !$this->follow_link)
        {
          continue;
        }

        if (is_dir($current_entry))
        {
          if (($this->type == 'directory' || $this->type == 'any') && ($depth >= $this->mindepth) && !$this->is_discarded($dir, $entryname) && $this->match_names($dir, $entryname) && $this->exec_ok($dir, $entryname))
          {
            $files[] = realpath($current_entry);
          }

          if (!$this->is_pruned($dir, $entryname))
          {
            $files = array_merge($files, $this->search_in($current_entry, $depth + 1));
          }
        }
        else
        {
          if (($this->type != 'directory' || $this->type == 'any') && ($depth >= $this->mindepth) && !$this->is_discarded($dir, $entryname) && $this->match_names($dir, $entryname) && $this->size_ok($dir, $entryname) && $this->exec_ok($dir, $entryname))
          {
            $files[] = realpath($current_entry);
          }
        }
      }
      closedir($current_dir);
    }

    return $files;
  }

  protected function match_names($dir, $entry)
  {
    if (!count($this->names)) return true;

    // we must match one "not_name" rules to be ko
    $one_not_name_rule = false;
    foreach ($this->names as $args)
    {
      list($not, $regex) = $args;
      if ($not)
      {
        $one_not_name_rule = true;
        if (preg_match($regex, $entry))
        {
          return false;
        }
      }
    }

    $one_name_rule = false;
    // we must match one "name" rules to be ok
    foreach ($this->names as $args)
    {
      list($not, $regex) = $args;
      if (!$not)
      {
        $one_name_rule = true;
        if (preg_match($regex, $entry))
        {
          return true;
        }
      }
    }

    if ($one_not_name_rule && $one_name_rule)
    {
      return false;
    }
    else if ($one_not_name_rule)
    {
      return true;
    }
    else if ($one_name_rule)
    {
      return false;
    }
    else
    {
      return true;
    }
  }

  protected function size_ok($dir, $entry)
  {
    if (!count($this->sizes)) return true;

    if (!is_file($dir.DIRECTORY_SEPARATOR.$entry)) return true;

    $filesize = filesize($dir.DIRECTORY_SEPARATOR.$entry);
    foreach ($this->sizes as $number_compare)
    {
      if (!$number_compare->test($filesize)) return false;
    }

    return true;
  }

  protected function is_pruned($dir, $entry)
  {
    if (!count($this->prunes)) return false;

    foreach ($this->prunes as $args)
    {
      $regex = $args[1];
      if (preg_match($regex, $entry)) return true;
    }

    return false;
  }

  protected function is_discarded($dir, $entry)
  {
    if (!count($this->discards)) return false;

    foreach ($this->discards as $args)
    {
      $regex = $args[1];
      if (preg_match($regex, $entry)) return true;
    }

    return false;
  }

  protected function exec_ok($dir, $entry)
  {
    if (!count($this->execs)) return true;

    foreach ($this->execs as $exec)
    {
      if (!call_user_func_array($exec, array($dir, $entry))) return false;
    }

    return true;
  }

  public static function isPathAbsolute($path)
  {
    if ($path{0} == '/' || $path{0} == '\\' ||
        (strlen($path) > 3 && ctype_alpha($path{0}) &&
         $path{1} == ':' &&
         ($path{2} == '\\' || $path{2} == '/')
        )
       )
    {
      return true;
    }

    return false;
  }
}

/**
 * Match globbing patterns against text.
 *
 *   if match_glob("foo.*", "foo.bar") echo "matched\n";
 *
 * // prints foo.bar and foo.baz
 * $regex = glob_to_regex("foo.*");
 * for (array('foo.bar', 'foo.baz', 'foo', 'bar') as $t)
 * {
 *   if (/$regex/) echo "matched: $car\n";
 * }
 *
 * sfGlobToRegex implements glob(3) style matching that can be used to match
 * against text, rather than fetching names from a filesystem.
 *
 * based on perl Text::Glob module.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@gmail.com> php port
 * @author     Richard Clamp <richardc@unixbeard.net> perl version
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@gmail.com>
 * @copyright  2002 Richard Clamp <richardc@unixbeard.net>
 * @version    SVN: $Id: sfFinder.class.php 3268 2007-01-13 20:19:33Z fabien $
 */
class sfGlobToRegex
{
  protected static $strict_leading_dot = true;
  protected static $strict_wildcard_slash = true;

  public static function setStrictLeadingDot($boolean)
  {
    self::$strict_leading_dot = $boolean;
  }

  public static function setStrictWildcardSlash($boolean)
  {
    self::$strict_wildcard_slash = $boolean;
  }

  /**
   * Returns a compiled regex which is the equiavlent of the globbing pattern.
   *
   * @param  string glob pattern
   * @return string regex
   */
  public static function glob_to_regex($glob)
  {
    $first_byte = true;
    $escaping = false;
    $in_curlies = 0;
    $regex = '';
    for ($i = 0; $i < strlen($glob); $i++)
    {
      $car = $glob[$i];
      if ($first_byte)
      {
        if (self::$strict_leading_dot && $car != '.')
        {
          $regex .= '(?=[^\.])';
        }

        $first_byte = false;
      }

      if ($car == '/')
      {
        $first_byte = true;
      }

      if ($car == '.' || $car == '(' || $car == ')' || $car == '|' || $car == '+' || $car == '^' || $car == '$')
      {
        $regex .= "\\$car";
      }
      else if ($car == '*')
      {
        $regex .= ($escaping ? "\\*" : (self::$strict_wildcard_slash ? "[^/]*" : ".*"));
      }
      else if ($car == '?')
      {
        $regex .= ($escaping ? "\\?" : (self::$strict_wildcard_slash ? "[^/]" : "."));
      }
      else if ($car == '{')
      {
        $regex .= ($escaping ? "\\{" : "(");
        if (!$escaping) ++$in_curlies;
      }
      else if ($car == '}' && $in_curlies)
      {
        $regex .= ($escaping ? "}" : ")");
        if (!$escaping) --$in_curlies;
      }
      else if ($car == ',' && $in_curlies)
      {
        $regex .= ($escaping ? "," : "|");
      }
      else if ($car == "\\")
      {
        if ($escaping)
        {
          $regex .= "\\\\";
          $escaping = false;
        }
        else
        {
          $escaping = true;
        }

        continue;
      }
      else
      {
        $regex .= $car;
        $escaping = false;
      }
      $escaping = false;
    }

    return "#^$regex$#";
  }
}

/**
 * Numeric comparisons.
 *
 * sfNumberCompare compiles a simple comparison to an anonymous
 * subroutine, which you can call with a value to be tested again.

 * Now this would be very pointless, if sfNumberCompare didn't understand
 * magnitudes.

 * The target value may use magnitudes of kilobytes (k, ki),
 * megabytes (m, mi), or gigabytes (g, gi).  Those suffixed
 * with an i use the appropriate 2**n version in accordance with the
 * IEC standard: http://physics.nist.gov/cuu/Units/binary.html
 *
 * based on perl Number::Compare module.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@gmail.com> php port
 * @author     Richard Clamp <richardc@unixbeard.net> perl version
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@gmail.com>
 * @copyright  2002 Richard Clamp <richardc@unixbeard.net>
 * @see        http://physics.nist.gov/cuu/Units/binary.html
 * @version    SVN: $Id: sfFinder.class.php 3268 2007-01-13 20:19:33Z fabien $
 */
class sfNumberCompare
{
  protected $test = '';

  public function __construct($test)
  {
    $this->test = $test;
  }

  public function test($number)
  {
    if (!preg_match('{^([<>]=?)?(.*?)([kmg]i?)?$}i', $this->test, $matches))
    {
      throw new sfException('don\'t understand "'.$this->test.'" as a test');
    }

    $target = array_key_exists(2, $matches) ? $matches[2] : '';
    $magnitude = array_key_exists(3, $matches) ? $matches[3] : '';
    if (strtolower($magnitude) == 'k')  $target *=           1000;
    if (strtolower($magnitude) == 'ki') $target *=           1024;
    if (strtolower($magnitude) == 'm')  $target *=        1000000;
    if (strtolower($magnitude) == 'mi') $target *=      1024*1024;
    if (strtolower($magnitude) == 'g')  $target *=     1000000000;
    if (strtolower($magnitude) == 'gi') $target *= 1024*1024*1024;

    $comparison = array_key_exists(1, $matches) ? $matches[1] : '==';
    if ($comparison == '==' || $comparison == '')
    {
      return ($number == $target);
    }
    else if ($comparison == '>')
    {
      return ($number > $target);
    }
    else if ($comparison == '>=')
    {
      return ($number >= $target);
    }
    else if ($comparison == '<')
    {
      return ($number < $target);
    }
    else if ($comparison == '<=')
    {
      return ($number <= $target);
    }

    return false;
  }
}
