<?php

/**
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com> php port
 * @author     Richard Clamp <richardc@unixbeard.net> perl version
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2002 Richard Clamp <richardc@unixbeard.net>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeGlobToRegex.class.php 1791 2006-08-23 21:17:06Z fabien $
 */

if (class_exists('pakeGlobToRegex'))
{
  return;
}

/**
 *
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
 * pakeGlobToRegex implements glob(3) style matching that can be used to match
 * against text, rather than fetching names from a filesystem.
 *
 * based on perl Text::Glob module.
 *
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com> php port
 * @author     Richard Clamp <richardc@unixbeard.net> perl version
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2002 Richard Clamp <richardc@unixbeard.net>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeGlobToRegex.class.php 1791 2006-08-23 21:17:06Z fabien $
 */
class pakeGlobToRegex
{
  private static $strict_leading_dot = true;
  private static $strict_wildcard_slash = true;

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
