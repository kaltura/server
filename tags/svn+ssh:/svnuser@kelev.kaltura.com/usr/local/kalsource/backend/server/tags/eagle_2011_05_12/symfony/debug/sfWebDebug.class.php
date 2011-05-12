<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebug creates debug information for easy debugging in the browser.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebug.class.php 3606 2007-03-13 19:01:45Z fabien $
 */
class sfWebDebug
{
  protected
    $log             = array(),
    $short_log       = array(),
    $max_priority    = 1000,
    $types           = array(),
    $last_time_log   = -1;

  protected static
    $instance        = null;

  public function initialize()
  {
  }

  /**
   * Retrieves the singleton instance of this class.
   *
   * @return sfWebDebug A sfWebDebug implementation instance
   */
  public static function getInstance()
  {
    if (!isset(self::$instance))
    {
      $class = __CLASS__;
      self::$instance = new $class();
      self::$instance->initialize();
    }

    return self::$instance;
  }

  /**
   * Registers javascripts and stylesheets needed for the web debug toolbar.
   */
  public function registerAssets()
  {
    $response = sfContext::getInstance()->getResponse();

    // register our css and js
    $response->addJavascript(sfConfig::get('sf_web_debug_web_dir').'/js/main');
    $response->addStylesheet(sfConfig::get('sf_web_debug_web_dir').'/css/main');
  }

  /**
   * Logs a short message to be displayed in the web debug toolbar.
   *
   * @param string The message string
   */
  public function logShortMessage($message)
  {
    $this->short_log[] = $message;
  }

  /**
   * Logs a message to the web debug toolbar.
   *
   * @param array An array of parameter
   *
   * @see sfWebDebugLogger
   */
  public function log($logEntry)
  {
    // elapsed time
    if ($this->last_time_log == -1)
    {
      $this->last_time_log = sfConfig::get('sf_timer_start');
    }

    $this->last_time_log = microtime(true);

    // update max priority
    if ($logEntry['priority'] < $this->max_priority)
    {
      $this->max_priority = $logEntry['priority'];
    }

    // update types
    if (!isset($this->types[$logEntry['type']]))
    {
      $this->types[$logEntry['type']] = 1;
    }
    else
    {
      ++$this->types[$logEntry['type']];
    }

    $this->log[] = $logEntry;
  }

  /**
   * Loads helpers needed for the web debug toolbar.
   */
  protected function loadHelpers()
  {
    sfLoader::loadHelpers(array('Helper', 'Url', 'Asset', 'Tag'));
  }

  /**
   * Formats a log line.
   *
   * @param string The log line to format
   *
   * @return string The formatted log lin
   */
  protected function formatLogLine($log_line)
  {
    static $constants;

    if (!$constants)
    {
      foreach (array('sf_app_dir', 'sf_root_dir', 'sf_symfony_lib_dir', 'sf_symfony_data_dir') as $constant)
      {
        $constants[realpath(sfConfig::get($constant)).DIRECTORY_SEPARATOR] = $constant.DIRECTORY_SEPARATOR;
      }
    }

    // escape HTML
    $log_line = htmlentities($log_line, ENT_QUOTES, sfConfig::get('sf_charset'));

    // replace constants value with constant name
    $log_line = str_replace(array_keys($constants), array_values($constants), $log_line);

    $log_line = sfToolkit::pregtr($log_line, array('/&quot;(.+?)&quot;/s' => '"<span class="sfWebDebugLogInfo">\\1</span>"',
                                                   '/^(.+?)\(\)\:/S'      => '<span class="sfWebDebugLogInfo">\\1()</span>:',
                                                   '/line (\d+)$/'        => 'line <span class="sfWebDebugLogInfo">\\1</span>'));

    // special formatting for SQL lines
    $log_line = preg_replace('/\b(SELECT|FROM|AS|LIMIT|ASC|COUNT|DESC|WHERE|LEFT JOIN|INNER JOIN|RIGHT JOIN|ORDER BY|GROUP BY|IN|LIKE|DISTINCT|DELETE|INSERT|INTO|VALUES)\b/', '<span class="sfWebDebugLogInfo">\\1</span>', $log_line);

    // remove username/password from DSN
    if (strpos($log_line, 'DSN') !== false)
    {
      $log_line = preg_replace("/=&gt;\s+'?[^'\s,]+'?/", "=&gt; '****'", $log_line);
    }

    return $log_line;
  }

  /**
   * Returns the web debug toolbar as HTML.
   *
   * @return string The web debug toolbar HTML
   */
  public function getResults()
  {
    if (!sfConfig::get('sf_web_debug'))
    {
      return '';
    }

    $this->loadHelpers();

    $result = '';

    // max priority
    $max_priority = '';
    if (sfConfig::get('sf_logging_enabled'))
    {
      $max_priority = $this->getPriority($this->max_priority);
    }

    $logs = '';
    $sql_logs = array();
    if (sfConfig::get('sf_logging_enabled'))
    {
      $logs = '<table class="sfWebDebugLogs">
        <tr>
          <th>#</th>
          <th>type</th>
          <th>message</th>
        </tr>'."\n";
      $line_nb = 0;
      foreach ($this->log as $logEntry)
      {
        $log = $logEntry['message'];

        $priority = $this->getPriority($logEntry['priority']);

        if (strpos($type = $logEntry['type'], 'sf') === 0)
        {
          $type = substr($type, 2);
        }

        // xdebug information
        $debug_info = '';
        if ($logEntry['debugStack'])
        {
          $debug_info .= '&nbsp;<a href="#" onclick="sfWebDebugToggle(\'debug_'.$line_nb.'\'); return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/toggle.gif').'</a><div class="sfWebDebugDebugInfo" id="debug_'.$line_nb.'" style="display:none">';
          foreach ($logEntry['debugStack'] as $i => $log_line)
          {
            $debug_info .= '#'.$i.' &raquo; '.$this->formatLogLine($log_line).'<br/>';
          }
          $debug_info .= "</div>\n";
        }

        // format log
        $log = $this->formatLogLine($log);

        // sql queries log
        if (preg_match('/execute(?:Query|Update).+?\:\s+(.+)$/', $log, $match))
        {
          $sql_logs[] .= $match[1];
        }

        ++$line_nb;
        $logs .= sprintf("<tr class='sfWebDebugLogLine sfWebDebug%s %s'><td class=\"sfWebDebugLogNumber\">%s</td><td class=\"sfWebDebugLogType\">%s&nbsp;%s</td><td>%s%s</td></tr>\n", 
          ucfirst($priority),
          $logEntry['type'],
          $line_nb,
          image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/'.$priority.'.png'),
          $type,
          $log,
          $debug_info
        );
      }
      $logs .= '</table>';

      ksort($this->types);
      $types = array();
      foreach ($this->types as $type => $nb)
      {
        $types[] = '<a href="#" onclick="sfWebDebugToggleMessages(\''.$type.'\'); return false;">'.$type.'</a>';
      }
    }

    // ignore cache link
    $cacheLink = '';
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_cache'))
    {
      $self_url = $_SERVER['PHP_SELF'].((strpos($_SERVER['PHP_SELF'], '_sf_ignore_cache') === false) ? '?_sf_ignore_cache=1' : '');
      $cacheLink = '<li><a href="'.$self_url.'" title="reload and ignore cache">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/reload.png').'</a></li>';
    }

    // logging information
    $logLink = '';
    if (sfConfig::get('sf_logging_enabled'))
    {
      $logLink = '<li><a href="#" onclick="sfWebDebugShowDetailsFor(\'sfWebDebugLog\'); return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/comment.png').' logs &amp; msgs</a></li>';
    }

    // database information
    $dbInfo = '';
    $dbInfoDetails = '';
    if ($sql_logs)
    {
      $dbInfo = '<li><a href="#" onclick="sfWebDebugShowDetailsFor(\'sfWebDebugDatabaseDetails\'); return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/database.png').' '.count($sql_logs).'</a></li>';

      $dbInfoDetails = '
        <div id="sfWebDebugDatabaseLogs">
        <ol><li>'.implode("</li>\n<li>", $sql_logs).'</li></ol>
        </div>
      ';
    }

    // memory used
    $memoryInfo = '';
    if (sfConfig::get('sf_debug') && function_exists('memory_get_usage'))
    {
      $total_memory = sprintf('%.1f', (memory_get_usage() / 1024));
      $memoryInfo = '<li>'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/memory.png').' '.$total_memory.' KB</li>';
    }

    // total time elapsed
    $timeInfo = '';
    if (sfConfig::get('sf_debug'))
    {
      $total_time = (microtime(true) - sfConfig::get('sf_timer_start')) * 1000;
      $total_time = sprintf(($total_time <= 1) ? '%.2f' : '%.0f', $total_time);
      $timeInfo = '<li class="last"><a href="#" onclick="sfWebDebugShowDetailsFor(\'sfWebDebugTimeDetails\'); return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/time.png').' '.$total_time.' ms</a></li>';
    }

    // timers
    $timeInfoDetails = '<table class="sfWebDebugLogs" style="width: 300px"><tr><th>type</th><th>calls</th><th>time (ms)</th></tr>';
    foreach (sfTimerManager::getTimers() as $name => $timer)
    {
      $timeInfoDetails .= sprintf('<tr><td class="sfWebDebugLogType">%s</td><td class="sfWebDebugLogNumber" style="text-align: right">%d</td><td style="text-align: right">%.2f</td></tr>', $name, $timer->getCalls(), $timer->getElapsedTime() * 1000);
    }
    $timeInfoDetails .= '</table>';

    // short log messages
    $short_messages = '';
    if ($this->short_log)
    {
      $short_messages = '<ul id="sfWebDebugShortMessages"><li>&raquo;&nbsp;'.implode('</li><li>&raquo&nbsp;', $this->short_log).'</li></ul>';
    }

    // logs
    $logInfo = '';
    if (sfConfig::get('sf_logging_enabled'))
    {
      $logInfo .= $short_messages.'
        <ul id="sfWebDebugLogMenu">
          <li><a href="#" onclick="sfWebDebugToggleAllLogLines(true, \'sfWebDebugLogLine\'); return false;">[all]</a></li>
          <li><a href="#" onclick="sfWebDebugToggleAllLogLines(false, \'sfWebDebugLogLine\'); return false;">[none]</a></li>
          <li><a href="#" onclick="sfWebDebugShowOnlyLogLines(\'info\'); return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/info.png').'</a></li>
          <li><a href="#" onclick="sfWebDebugShowOnlyLogLines(\'warning\'); return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/warning.png').'</a></li>
          <li><a href="#" onclick="sfWebDebugShowOnlyLogLines(\'error\'); return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/error.png').'</a></li>
          <li>'.implode("</li>\n<li>", $types).'</li>
        </ul>
        <div id="sfWebDebugLogLines">'.$logs.'</div>
      ';
    }

    $result .= '
    <div id="sfWebDebug">
      <div id="sfWebDebugBar" class="sfWebDebug'.ucfirst($max_priority).'">
        <a href="#" onclick="sfWebDebugToggleMenu(); return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/sf.png').'</a>
        <ul id="sfWebDebugDetails" class="menu">
          <li>'.file_get_contents(sfConfig::get('sf_symfony_lib_dir').'/VERSION').'</li>
          <li><a href="#" onclick="sfWebDebugShowDetailsFor(\'sfWebDebugConfig\'); return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/config.png').' vars &amp; config</a></li>
          '.$cacheLink.'
          '.$logLink.'
          '.$dbInfo.'
          '.$memoryInfo.'
          '.$timeInfo.'
        </ul>
        <a href="#" onclick="document.getElementById(\'sfWebDebug\').style.display=\'none\'; return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/close.png').'</a>
      </div>

      <div id="sfWebDebugLog" class="top" style="display: none"><h1>Log and debug messages</h1>'.$logInfo.'</div>
      <div id="sfWebDebugConfig" class="top" style="display: none"><h1>Configuration and request variables</h1>'.$this->getCurrentConfigAsHtml().'</div>
      <div id="sfWebDebugDatabaseDetails" class="top" style="display: none"><h1>SQL queries</h1>'.$dbInfoDetails.'</div>
      <div id="sfWebDebugTimeDetails" class="top" style="display: none"><h1>Timers</h1>'.$timeInfoDetails.'</div>

      </div>
    ';

    return $result;
  }

  /**
   * Returns the current configuration as HTML.
   *
   * @return string The current configuration as HTML
   */
  protected function getCurrentConfigAsHtml()
  {
    $config = array(
      'debug'        => sfConfig::get('sf_debug')             ? 'on' : 'off',
      'xdebug'       => (extension_loaded('xdebug'))          ? 'on' : 'off',
      'logging'      => sfConfig::get('sf_logging_enabled')   ? 'on' : 'off',
      'cache'        => sfConfig::get('sf_cache')             ? 'on' : 'off',
      'eaccelerator' => (extension_loaded('eaccelerator') && ini_get('eaccelerator.enable')) ? 'on' : 'off',
      'apc'          => (extension_loaded('apc') && ini_get('apc.enabled')) ? 'on' : 'off',
      'xcache'       => (extension_loaded('xcache') && ini_get('xcache.cacher')) ? 'on' : 'off',
      'compression'  => sfConfig::get('sf_compressed')        ? 'on' : 'off',
      'syck'         => (extension_loaded('syck'))            ? 'on' : 'off',
    );

    $result = '<ul id="sfWebDebugConfigSummary">';
    foreach ($config as $key => $value)
    {
      $result .= '<li class="is'.$value.''.($key == 'syck' ? ' last' : '').'">'.$key.'</li>';
    }
    $result .= '</ul>';

    $context = sfContext::getInstance();
    $result .= $this->formatArrayAsHtml('request',  sfDebug::requestAsArray($context->getRequest()));
    $result .= $this->formatArrayAsHtml('response', sfDebug::responseAsArray($context->getResponse()));
    $result .= $this->formatArrayAsHtml('settings', sfDebug::settingsAsArray());
    $result .= $this->formatArrayAsHtml('globals',  sfDebug::globalsAsArray());
    $result .= $this->formatArrayAsHtml('php',      sfDebug::phpInfoAsArray());

    return $result;
  }

  /**
   * Converts an array to HTML.
   *
   * @param string The identifier to use
   * @param array  The array of values
   *
   * @return string An HTML string
   */
  protected function formatArrayAsHtml($id, $values)
  {
    $id = ucfirst(strtolower($id));
    $content = '
    <h2>'.$id.' <a href="#" onclick="sfWebDebugToggle(\'sfWebDebug'.$id.'\'); return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/toggle.gif').'</a></h2>
    <div id="sfWebDebug'.$id.'" style="display: none"><pre>'.htmlentities(@sfYaml::Dump($values), ENT_QUOTES, sfConfig::get('sf_charset')).'</pre></div>
    ';

    return $content;
  }

  /**
   * Decorates a chunk of HTML with cache information.
   *
   * @param string  The internalUri representing the content
   * @param string  The HTML content
   * @param boolean true if the content is new in the cache, false otherwise
   *
   * @return string The decorated HTML string
   */
  public function decorateContentWithDebug($internalUri, $content, $new = false)
  {
    $context = sfContext::getInstance();

    // don't decorate if not html or if content is null
    if (!sfConfig::get('sf_web_debug') || !$content || false === strpos($context->getResponse()->getContentType(), 'html'))
    {
      return $content;
    }

    $cache = $context->getViewCacheManager();
    $this->loadHelpers();

    $bg_color      = $new ? '#9ff' : '#ff9';
    $last_modified = $cache->lastModified($internalUri);
    $id            = md5($internalUri);
    $content = '
      <div id="main_'.$id.'" class="sfWebDebugActionCache" style="border: 1px solid #f00">
      <div id="sub_main_'.$id.'" class="sfWebDebugCache" style="background-color: '.$bg_color.'; border-right: 1px solid #f00; border-bottom: 1px solid #f00;">
      <div style="height: 16px; padding: 2px"><a href="#" onclick="sfWebDebugToggle(\''.$id.'\'); return false;"><strong>cache information</strong></a>&nbsp;<a href="#" onclick="sfWebDebugToggle(\'sub_main_'.$id.'\'); document.getElementById(\'main_'.$id.'\').style.border = \'none\'; return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/close.png').'</a>&nbsp;</div>
        <div style="padding: 2px; display: none" id="'.$id.'">
        [uri]&nbsp;'.$internalUri.'<br />
        [life&nbsp;time]&nbsp;'.$cache->getLifeTime($internalUri).'&nbsp;seconds<br />
        [last&nbsp;modified]&nbsp;'.(time() - $last_modified).'&nbsp;seconds<br />
        &nbsp;<br />&nbsp;
        </div>
      </div><div>
      '.$content.'
      </div></div>
    ';

    return $content;
  }

  /**
   * Converts a proprity value to a string.
   *
   * @param integer The priority value
   *
   * @return string The priority as a string
   */
  protected function getPriority($value)
  {
    if ($value >= 6)
    {
      return 'info';
    }
    else if ($value >= 4)
    {
      return 'warning';
    }
    else
    {
      return 'error';
    }
  }
}
