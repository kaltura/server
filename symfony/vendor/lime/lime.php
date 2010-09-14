<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Unit test library.
 *
 * @package    lime
 * @author     Fabien Potencier <fabien.potencier@gmail.com>
 * @version    SVN: $Id: lime.php 3298 2007-01-17 06:08:15Z fabien $
 */

class lime_test
{
  public $plan = null;
  public $test_nb = 0;
  public $failed = 0;
  public $passed = 0;
  public $skipped = 0;
  public $output = null;

  function __construct($plan = null, $output_instance = null)
  {
    $this->plan = $plan;
    $this->output = $output_instance ? $output_instance : new lime_output();

    null !== $this->plan and $this->output->echoln(sprintf("1..%d", $this->plan));
  }

  function __destruct()
  {
    $total = $this->passed + $this->failed + $this->skipped;

    null === $this->plan and $this->plan = $total and $this->output->echoln(sprintf("1..%d", $this->plan));

    if ($total > $this->plan)
    {
      $this->output->diag(sprintf("Looks like you planned %d tests but ran %d extra.", $this->plan, $total - $this->plan));
    }
    elseif ($total < $this->plan)
    {
      $this->output->diag(sprintf("Looks like you planned %d tests but only ran %d.", $this->plan, $total));
    }

    if ($this->failed)
    {
      $this->output->diag(sprintf("Looks like you failed %d tests of %d.", $this->failed, $this->plan));
    }

    flush();
  }

  function ok($exp, $message = '')
  {
    if ($result = (boolean) $exp)
    {
      ++$this->passed;
    }
    else
    {
      ++$this->failed;
    }
    $this->output->echoln(sprintf("%s %d%s", $result ? 'ok' : 'not ok', ++$this->test_nb, $message = $message ? sprintf('%s %s', 0 === strpos($message, '#') ? '' : ' -', $message) : ''));

    if (!$result)
    {
      $traces = debug_backtrace();
      if ($_SERVER['PHP_SELF'])
      {
        $i = strstr($traces[0]['file'], $_SERVER['PHP_SELF']) ? 0 : 1;
      }
      else
      {
        $i = 0;
      }
      $this->output->diag(sprintf('    Failed test (%s at line %d)', str_replace(getcwd(), '.', $traces[$i]['file']), $traces[$i]['line']));
    }

    return $result;
  }

  function is($exp1, $exp2, $message = '')
  {
    if (is_object($exp1) || is_object($exp2))
    {
      $value = $exp1 === $exp2;
    }
    else
    {
      $value = $exp1 == $exp2;
    }

    if (!$result = $this->ok($value, $message))
    {
      $this->output->diag(sprintf("           got: %s", str_replace("\n", '', var_export($exp1, true))), sprintf("      expected: %s", str_replace("\n", '', var_export($exp2, true))));
    }

    return $result;
  }

  function isnt($exp1, $exp2, $message = '')
  {
    if (!$result = $this->ok($exp1 != $exp2, $message))
    {
      $this->output->diag(sprintf("      %s", str_replace("\n", '', var_export($exp1, true))), '          ne', sprintf("      %s", str_replace("\n", '', var_export($exp2, true))));
    }

    return $result;
  }

  function like($exp, $regex, $message = '')
  {
    if (!$result = $this->ok(preg_match($regex, $exp), $message))
    {
      $this->output->diag(sprintf("                    '%s'", $exp), sprintf("      doesn't match '%s'", $regex));
    }

    return $result;
  }

  function unlike($exp, $regex, $message = '')
  {
    if (!$result = $this->ok(!preg_match($regex, $exp), $message))
    {
      $this->output->diag(sprintf("               '%s'", $exp), sprintf("      matches '%s'", $regex));
    }

    return $result;
  }

  function cmp_ok($exp1, $op, $exp2, $message = '')
  {
    eval(sprintf("\$result = \$exp1 $op \$exp2;"));
    if (!$this->ok($result, $message))
    {
      $this->output->diag(sprintf("      %s", str_replace("\n", '', var_export($exp1, true))), sprintf("          %s", $op), sprintf("      %s", str_replace("\n", '', var_export($exp2, true))));
    }

    return $result;
  }

  function can_ok($object, $methods, $message = '')
  {
    $result = true;
    $failed_messages = array();
    foreach ((array) $methods as $method)
    {
      if (!method_exists($object, $method))
      {
        $failed_messages[] = sprintf("      method '%s' does not exist", $method);
        $result = false;
      }
    }

    !$this->ok($result, $message);

    !$result and $this->output->diag($failed_messages);

    return $result;
  }

  function isa_ok($var, $class, $message = '')
  {
    $type = is_object($var) ? get_class($var) : gettype($var);
    if (!$result = $this->ok($type == $class, $message))
    {
      $this->output->diag(sprintf("      isa_ok isn't a '%s' it's a '%s'", $class, $type));
    }

    return $result;
  }

  function is_deeply($exp1, $exp2, $message = '')
  {
    if (!$result = $this->ok($this->test_is_deeply($exp1, $exp2), $message))
    {
      $this->output->diag(sprintf("           got: %s", str_replace("\n", '', var_export($exp1, true))), sprintf("      expected: %s", str_replace("\n", '', var_export($exp2, true))));
    }

    return $result;
  }

  function pass($message = '')
  {
    return $this->ok(true, $message);
  }

  function fail($message = '')
  {
    return $this->ok(false, $message);
  }

  function diag($message)
  {
    $this->output->diag($message);
  }

  function skip($message = '', $nb_tests = 1)
  {
    for ($i = 0; $i < $nb_tests; $i++)
    {
      ++$this->skipped and --$this->passed;
      $this->pass(sprintf("# SKIP%s", $message ? ' '.$message : ''));
    }
  }

  function todo($message = '')
  {
    ++$this->skipped and --$this->passed;
    $this->pass(sprintf("# TODO%s", $message ? ' '.$message : ''));
  }

  function include_ok($file, $message = '')
  {
    if (!$result = $this->ok((@include($file)) == 1, $message))
    {
      $this->output->diag(sprintf("      Tried to include '%s'", $file));
    }

    return $result;
  }

  private function test_is_deeply($var1, $var2)
  {
    if (gettype($var1) != gettype($var2))
    {
      return false;
    }

    if (is_array($var1))
    {
      ksort($var1);
      ksort($var2);
      if (array_diff(array_keys($var1), array_keys($var2)))
      {
        return false;
      }
      $is_equal = true;
      foreach ($var1 as $key => $value)
      {
        $is_equal = $this->test_is_deeply($var1[$key], $var2[$key]);
        if ($is_equal === false)
        {
          break;
        }
      }

      return $is_equal;
    }
    else
    {
      return $var1 === $var2;
    }
  }

  function comment($message)
  {
    $this->output->comment($message);
  }

  static function get_temp_directory()
  {
    if ('\\' == DIRECTORY_SEPARATOR)
    {
      foreach (array('TEMP', 'TMP', 'windir') as $dir)
      {
        if ($var = isset($_ENV[$dir]) ? $_ENV[$dir] : getenv($dir))
        {
          return $var;
        }
      }

      return getenv('SystemRoot').'\temp';
    }

    if ($var = isset($_ENV['TMPDIR']) ? $_ENV['TMPDIR'] : getenv('TMPDIR'))
    {
      return $var;
    }

    return '/tmp';
  }
}

class lime_output
{
  function diag()
  {
    $messages = func_get_args();
    foreach ($messages as $message)
    {
      array_map(array($this, 'comment'), (array) $message);
    }
  }

  function comment($message)
  {
    echo "# $message\n";
  }

  function echoln($message)
  {
    echo "$message\n";
  }
}

class lime_output_color extends lime_output
{
  public $colorizer = null;

  function __construct()
  {
    $this->colorizer = new lime_colorizer();
  }

  function diag()
  {
    $messages = func_get_args();
    foreach ($messages as $message)
    {
      echo $this->colorizer->colorize('# '.join("\n# ", (array) $message), 'COMMENT')."\n";
    }
  }

  function comment($message)
  {
    echo $this->colorizer->colorize(sprintf('# %s', $message), 'COMMENT')."\n";
  }

  function echoln($message, $colorizer_parameter = null)
  {
    $message = preg_replace('/(?:^|\.)((?:not ok|dubious) *\d*)\b/e', '$this->colorizer->colorize(\'$1\', \'ERROR\')', $message);
    $message = preg_replace('/(?:^|\.)(ok *\d*)\b/e', '$this->colorizer->colorize(\'$1\', \'INFO\')', $message);
    $message = preg_replace('/"(.+?)"/e', '$this->colorizer->colorize(\'$1\', \'PARAMETER\')', $message);
    $message = preg_replace('/(\->|\:\:)?([a-zA-Z0-9_]+?)\(\)/e', '$this->colorizer->colorize(\'$1$2()\', \'PARAMETER\')', $message);

    echo ($colorizer_parameter ? $this->colorizer->colorize($message, $colorizer_parameter) : $message)."\n";
  }
}

class lime_colorizer
{
  static public $styles = array();

  static function style($name, $options = array())
  {
    self::$styles[$name] = $options;
  }

  static function colorize($text = '', $parameters = array())
  {
    // disable colors if not supported (windows or non tty console)
    if (DIRECTORY_SEPARATOR == '\\' || !function_exists('posix_isatty') || !@posix_isatty(STDOUT))
    {
      return $text;
    }

    static $options    = array('bold' => 1, 'underscore' => 4, 'blink' => 5, 'reverse' => 7, 'conceal' => 8);
    static $foreground = array('black' => 30, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37);
    static $background = array('black' => 40, 'red' => 41, 'green' => 42, 'yellow' => 43, 'blue' => 44, 'magenta' => 45, 'cyan' => 46, 'white' => 47);

    !is_array($parameters) && isset(self::$styles[$parameters]) and $parameters = self::$styles[$parameters];

    $codes = array();
    isset($parameters['fg']) and $codes[] = $foreground[$parameters['fg']];
    isset($parameters['bg']) and $codes[] = $background[$parameters['bg']];
    foreach ($options as $option => $value)
    {
      isset($parameters[$option]) && $parameters[$option] and $codes[] = $value;
    }

    return "\033[".implode(';', $codes).'m'.$text."\033[0m";
  }
}

lime_colorizer::style('ERROR', array('bg' => 'red', 'fg' => 'white', 'bold' => true));
lime_colorizer::style('INFO',  array('fg' => 'green', 'bold' => true));
lime_colorizer::style('PARAMETER', array('fg' => 'cyan'));
lime_colorizer::style('COMMENT',  array('fg' => 'yellow'));

class lime_harness extends lime_registration
{
  public $php_cli = '';
  public $stats = array();
  public $output = null;

  function __construct($output_instance, $php_cli = null)
  {
    $this->php_cli = null === $php_cli ? PHP_BINDIR.DIRECTORY_SEPARATOR.'php' : $php_cli;
    if (!is_executable($this->php_cli))
    {
      $this->php_cli = $this->find_php_cli();
    }

    $this->output = $output_instance ? $output_instance : new lime_output();
  }

  protected function find_php_cli()
  {
    $path = getenv('PATH') ? getenv('PATH') : getenv('Path');
    $exe_suffixes = DIRECTORY_SEPARATOR == '\\' ? (getenv('PATHEXT') ? explode(PATH_SEPARATOR, getenv('PATHEXT')) : array('.exe', '.bat', '.cmd', '.com')) : array('');
    foreach (array('php5', 'php') as $php_cli)
    {
      foreach ($exe_suffixes as $suffix)
      {
        foreach (explode(PATH_SEPARATOR, $path) as $dir)
        {
          $file = $dir.DIRECTORY_SEPARATOR.$php_cli.$suffix;
          if (is_executable($file))
          {
            return $file;
          }
        }
      }
    }

    throw new Exception("Unable to find PHP executable.");
  }

  function run()
  {
    if (!count($this->files))
    {
      throw new Exception('You must register some test files before running them!');
    }

    // sort the files to be able to predict the order
    sort($this->files);

    $this->stats =array(
      '_failed_files' => array(),
      '_failed_tests' => 0,
      '_nb_tests'     => 0,
    );

    foreach ($this->files as $file)
    {
      $this->stats[$file] = array(
        'plan'     =>   null,
        'nb_tests' => 0,
        'failed'   => array(),
        'passed'   => array(),
      );
      $this->current_file = $file;
      $this->current_test = 0;
      $relative_file = $this->get_relative_file($file);

      ob_start(array($this, 'process_test_output'));
      passthru(sprintf('%s -d html_errors=off -d open_basedir= -q "%s" 2>&1', $this->php_cli, $file), $return);
      ob_end_clean();

      if ($return > 0)
      {
        $this->stats[$file]['status'] = 'dubious';
        $this->stats[$file]['status_code'] = $return;
      }
      else
      {
        $delta = $this->stats[$file]['plan'] - $this->stats[$file]['nb_tests'];
        if ($delta > 0)
        {
          $this->output->echoln(sprintf('%s%s%s', substr($relative_file, -67), str_repeat('.', 70 - min(67, strlen($relative_file))), $this->output->colorizer->colorize(sprintf('# Looks like you planned %d tests but only ran %d.', $this->stats[$file]['plan'], $this->stats[$file]['nb_tests']), 'COMMENT')));
          $this->stats[$file]['status'] = 'dubious';
          $this->stats[$file]['status_code'] = 255;
          $this->stats['_nb_tests'] += $delta;
          for ($i = 1; $i <= $delta; $i++)
          {
            $this->stats[$file]['failed'][] = $this->stats[$file]['nb_tests'] + $i;
          }
        }
        else if ($delta < 0)
        {
          $this->output->echoln(sprintf('%s%s%s', substr($relative_file, -67), str_repeat('.', 70 - min(67, strlen($relative_file))), $this->output->colorizer->colorize(sprintf('# Looks like you planned %s test but ran %s extra.', $this->stats[$file]['plan'], $this->stats[$file]['nb_tests'] - $this->stats[$file]['plan']), 'COMMENT')));
          $this->stats[$file]['status'] = 'dubious';
          $this->stats[$file]['status_code'] = 255;
          for ($i = 1; $i <= -$delta; $i++)
          {
            $this->stats[$file]['failed'][] = $this->stats[$file]['plan'] + $i;
          }
        }
        else
        {
          $this->stats[$file]['status_code'] = 0;
          $this->stats[$file]['status'] = $this->stats[$file]['failed'] ? 'not ok' : 'ok';
        }
      }

      $this->output->echoln(sprintf('%s%s%s', substr($relative_file, -67), str_repeat('.', 70 - min(67, strlen($relative_file))), $this->stats[$file]['status']));
      if (($nb = count($this->stats[$file]['failed'])) || $return > 0)
      {
        if ($nb)
        {
          $this->output->echoln(sprintf("    Failed tests: %s", implode(', ', $this->stats[$file]['failed'])));
        }
        $this->stats['_failed_files'][] = $file;
        $this->stats['_failed_tests']  += $nb;
      }

      if ('dubious' == $this->stats[$file]['status'])
      {
        $this->output->echoln(sprintf('    Test returned status %s', $this->stats[$file]['status_code']));
      }
    }

    if (count($this->stats['_failed_files']))
    {
      $format = "%-30s  %4s  %5s  %5s  %s";
      $this->output->echoln(sprintf($format, 'Failed Test', 'Stat', 'Total', 'Fail', 'List of Failed'));
      $this->output->echoln("------------------------------------------------------------------");
      foreach ($this->stats as $file => $file_stat)
      {
        if (!in_array($file, $this->stats['_failed_files'])) continue;

        $this->output->echoln(sprintf($format, substr($this->get_relative_file($file), -30), $file_stat['status_code'], count($file_stat['failed']) + count($file_stat['passed']), count($file_stat['failed']), implode(' ', $file_stat['failed'])));
      }

      $this->output->echoln(sprintf('Failed %d/%d test scripts, %.2f%% okay. %d/%d subtests failed, %.2f%% okay.',
        $nb_failed_files = count($this->stats['_failed_files']),
        $nb_files = count($this->files),
        ($nb_files - $nb_failed_files) * 100 / $nb_files,
        $nb_failed_tests = $this->stats['_failed_tests'],
        $nb_tests = $this->stats['_nb_tests'],
        $nb_tests > 0 ? ($nb_tests - $nb_failed_tests) * 100 / $nb_tests : 0
      ), 'ERROR');
    }
    else
    {
      $this->output->echoln('All tests successful.', 'INFO');
      $this->output->echoln(sprintf('Files=%d, Tests=%d', count($this->files), $this->stats['_nb_tests']), 'INFO');
    }

    return $this->stats['_failed_tests'] ? false : true;
  }

  private function process_test_output($lines)
  {
    foreach (explode("\n", $lines) as $text)
    {
      if (false !== strpos($text, 'not ok '))
      {
        ++$this->current_test;
        $test_number = (int) substr($text, 7);
        $this->stats[$this->current_file]['failed'][] = $test_number;

        ++$this->stats[$this->current_file]['nb_tests'];
        ++$this->stats['_nb_tests'];
      }
      else if (false !== strpos($text, 'ok '))
      {
        ++$this->stats[$this->current_file]['nb_tests'];
        ++$this->stats['_nb_tests'];
      }
      else if (preg_match('/^1\.\.(\d+)/', $text, $match))
      {
        $this->stats[$this->current_file]['plan'] = $match[1];
      }
    }

    return;
  }
}

class lime_coverage extends lime_registration
{
  public $files = array();
  public $extension = '.php';
  public $base_dir = '';
  public $harness = null;
  public $verbose = false;

  function __construct($harness)
  {
    $this->harness = $harness;
  }

  function run()
  {
    if (!function_exists('xdebug_start_code_coverage'))
    {
      throw new Exception('You must install and enable xdebug before using lime coverage.');
    }

    if (!count($this->harness->files))
    {
      throw new Exception('You must register some test files before running coverage!');
    }

    if (!count($this->files))
    {
      throw new Exception('You must register some files to cover!');
    }

    $coverage = array();
    $tmp_file = lime_test::get_temp_directory().DIRECTORY_SEPARATOR.'test.php';
    foreach ($this->harness->files as $file)
    {
      $tmp = <<<EOF
<?php
xdebug_start_code_coverage();
ob_start();
include('$file');
ob_end_clean();
echo '<PHP_SER>'.serialize(xdebug_get_code_coverage()).'</PHP_SER>';
EOF;
      file_put_contents($tmp_file, $tmp);
      ob_start();
      passthru(sprintf('%s -d html_errors=off -d open_basedir= -q "%s" 2>&1', $this->harness->php_cli, $tmp_file), $return);
      $retval = ob_get_clean();
      if (0 == $return)
      {
        if (false === $cov = unserialize(substr($retval, strpos($retval, '<PHP_SER>') + 9, strpos($retval, '</PHP_SER>') - 9)))
        {
          throw new Exception(sprintf('Unable to unserialize coverage for file "%s"', $file));
        }

        foreach ($cov as $file => $lines)
        {
          if (!isset($coverage[$file]))
          {
            $coverage[$file] = array();
          }

          foreach ($lines as $line => $count)
          {
            if (!isset($coverage[$file][$line]))
            {
              $coverage[$file][$line] = 0;
            }
            $coverage[$file][$line] = $coverage[$file][$line] + $count;
          }
        }
      }
    }
    unlink($tmp_file);

    ksort($coverage);
    $total_php_lines = 0;
    $total_covered_lines = 0;
    foreach ($this->files as $file)
    {
      $cov = isset($coverage[$file]) ? $coverage[$file] : array();

      list($cov, $php_lines) = $this->compute(file_get_contents($file), $cov);

      $output = $this->harness->output;
      $percent = count($php_lines) ? count($cov) * 100 / count($php_lines) : 100;

      $total_php_lines += count($php_lines);
      $total_covered_lines += count($cov);

      $output->echoln(sprintf("%-70s %3.0f%%", substr($this->get_relative_file($file), -70), $percent), $percent == 100 ? 'INFO' : ($percent > 90 ? 'PARAMETER' : ($percent < 20 ? 'ERROR' : '')));
      if ($this->verbose && $percent != 100)
      {
        $output->comment(sprintf("missing: %s", $this->format_range(array_keys(array_diff_key($php_lines, $cov)))));
      }
    }

    $output->echoln(sprintf("TOTAL COVERAGE: %3.0f%%", $total_covered_lines * 100 / $total_php_lines));
  }

  static function get_php_lines($content)
  {
    if (is_file($content))
    {
      $content = file_get_contents($content);
    }

    $tokens = token_get_all($content);
    $php_lines = array();
    $current_line = 1;
    $in_class = false;
    $in_function = false;
    $in_function_declaration = false;
    $end_of_current_expr = true;
    $open_braces = 0;
    foreach ($tokens as $token)
    {
      if (is_string($token))
      {
        switch ($token)
        {
          case '=':
            if (false === $in_class || (false !== $in_function && !$in_function_declaration))
            {
              $php_lines[$current_line] = true;
            }
            break;
          case '{':
            ++$open_braces;
            $in_function_declaration = false;
            break;
          case ';':
            $in_function_declaration = false;
            $end_of_current_expr = true;
            break;
          case '}':
            $end_of_current_expr = true;
            --$open_braces;
            if ($open_braces == $in_class)
            {
              $in_class = false;
            }
            if ($open_braces == $in_function)
            {
              $in_function = false;
            }
            break;
        }

        continue;
      }

      list($id, $text) = $token;

      switch ($id)
      {
        case T_CURLY_OPEN:
        case T_DOLLAR_OPEN_CURLY_BRACES:
          ++$open_braces;
          break;
        case T_WHITESPACE:
        case T_OPEN_TAG:
        case T_CLOSE_TAG:
          $end_of_current_expr = true;
          $current_line += count(explode("\n", $text)) - 1;
          break;
        case T_COMMENT:
        case T_DOC_COMMENT:
          $current_line += count(explode("\n", $text)) - 1;
          break;
        case T_CLASS:
          $in_class = $open_braces;
          break;
        case T_FUNCTION:
          $in_function = $open_braces;
          $in_function_declaration = true;
          break;
        case T_AND_EQUAL:
        case T_CASE:
        case T_CATCH:
        case T_CLONE:
        case T_CONCAT_EQUAL:
        case T_CONTINUE:
        case T_DEC:
        case T_DECLARE:
        case T_DEFAULT:
        case T_DIV_EQUAL:
        case T_DO:
        case T_ECHO:
        case T_ELSEIF:
        case T_EMPTY:
        case T_ENDDECLARE:
        case T_ENDFOR:
        case T_ENDFOREACH:
        case T_ENDIF:
        case T_ENDSWITCH:
        case T_ENDWHILE:
        case T_EVAL:
        case T_EXIT:
        case T_FOR:
        case T_FOREACH:
        case T_GLOBAL:
        case T_IF:
        case T_INC:
        case T_INCLUDE:
        case T_INCLUDE_ONCE:
        case T_INSTANCEOF:
        case T_ISSET:
        case T_IS_EQUAL:
        case T_IS_GREATER_OR_EQUAL:
        case T_IS_IDENTICAL:
        case T_IS_NOT_EQUAL:
        case T_IS_NOT_IDENTICAL:
        case T_IS_SMALLER_OR_EQUAL:
        case T_LIST:
        case T_LOGICAL_AND:
        case T_LOGICAL_OR:
        case T_LOGICAL_XOR:
        case T_MINUS_EQUAL:
        case T_MOD_EQUAL:
        case T_MUL_EQUAL:
        case T_NEW:
        case T_OBJECT_OPERATOR:
        case T_OR_EQUAL:
        case T_PLUS_EQUAL:
        case T_PRINT:
        case T_REQUIRE:
        case T_REQUIRE_ONCE:
        case T_RETURN:
        case T_SL:
        case T_SL_EQUAL:
        case T_SR:
        case T_SR_EQUAL:
        case T_THROW:
        case T_TRY:
        case T_UNSET:
        case T_UNSET_CAST:
        case T_USE:
        case T_WHILE:
        case T_XOR_EQUAL:
          $php_lines[$current_line] = true;
          $end_of_current_expr = false;
          break;
        default:
          if (false === $end_of_current_expr)
          {
            $php_lines[$current_line] = true;
          }
          //print "$current_line: ".token_name($id)."\n";
      }
    }

    return $php_lines;
  }

  function compute($content, $cov)
  {
    $php_lines = self::get_php_lines($content);

    // we remove from $cov non php lines
    foreach (array_diff_key($cov, $php_lines) as $line => $tmp)
    {
      unset($cov[$line]);
    }

    return array($cov, $php_lines);
  }

  function format_range($lines)
  {
    sort($lines);
    $formatted = '';
    $first = -1;
    $last = -1;
    foreach ($lines as $line)
    {
      if ($last + 1 != $line)
      {
        if ($first != -1)
        {
          $formatted .= $first == $last ? "$first " : "[$first - $last] ";
        }
        $first = $line;
        $last = $line;
      }
      else
      {
        $last = $line;
      }
    }
    if ($first != -1)
    {
      $formatted .= $first == $last ? "$first " : "[$first - $last] ";
    }

    return $formatted;
  }
}

class lime_registration
{
  public $files = array();
  public $extension = '.php';
  public $base_dir = '';

  function register($files_or_directories)
  {
    foreach ((array) $files_or_directories as $f_or_d)
    {
      if (is_file($f_or_d))
      {
        $this->files[] = realpath($f_or_d);
      }
      elseif (is_dir($f_or_d))
      {
        $this->register_dir($f_or_d);
      }
      else
      {
        throw new Exception(sprintf('The file or directory "%s" does not exist.', $f_or_d));
      }
    }
  }

  function register_glob($glob)
  {
    if ($dirs = glob($glob))
    {
      foreach ($dirs as $file)
      {
        $this->files[] = realpath($file);
      }
    }
  }

  function register_dir($directory)
  {
    if (!is_dir($directory))
    {
      throw new Exception(sprintf('The directory "%s" does not exist.', $directory));
    }

    $files = array();

    $current_dir = opendir($directory);
    while ($entry = readdir($current_dir))
    {
      if ($entry == '.' || $entry == '..') continue;

      if (is_dir($entry))
      {
        $this->register_dir($entry);
      }
      elseif (preg_match('#'.$this->extension.'$#', $entry))
      {
        $files[] = realpath($directory.DIRECTORY_SEPARATOR.$entry);
      }
    }

    $this->files = array_merge($this->files, $files);
  }

  protected function get_relative_file($file)
  {
    return str_replace(DIRECTORY_SEPARATOR, '/', str_replace(array(realpath($this->base_dir).DIRECTORY_SEPARATOR, $this->extension), '', $file));
  }
}
