<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfLoggingConfigHandler allows you to configure logging and register loggers with the system.
 *
 * @package    symfony
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfLoggingConfigHandler.class.php 3258 2007-01-13 12:12:22Z fabien $
 */
class sfLoggingConfigHandler extends sfDefineEnvironmentConfigHandler
{
  protected
    $enabled = true,
    $loggers = array();

  /**
   * Executes this configuration handler.
   *
   * @param array An array of absolute filesystem path to a configuration file
   *
   * @return string Data to be written to a cache file
   */
  public function execute($configFiles)
  {
    $data = parent::execute($configFiles);

    if ($this->enabled)
    {
      $data .= "\n\$logger = sfLogger::getInstance();\n";

      // log level
      $data .= "\$logger->setLogLevel(constant('SF_LOG_'.strtoupper(sfConfig::get('sf_logging_level'))));\n";

      // register loggers defined in the logging.yml configuration file
      foreach ($this->loggers as $name => $keys)
      {
        if (isset($keys['enabled']) && !$keys['enabled'])
        {
          continue;
        }

        if (!isset($keys['class']))
        {
          // missing class key
          throw new sfParseException(sprintf('Configuration file "%s" specifies filter "%s" with missing class key', $configFiles[0], $name));
        }

        $condition = true;
        if (isset($keys['param']['condition']))
        {
          $condition = $this->replaceConstants($keys['param']['condition']);
          unset($keys['param']['condition']);
        }

        if ($condition)
        {
          // parse parameters
          $parameters = isset($keys['param']) ? var_export($keys['param'], true) : '';

          // create logger instance
          $data .= sprintf("\n\$log = new %s();\n\$log->initialize(%s);\n\$logger->registerLogger(\$log);\n", $keys['class'], $parameters);
        }
      }
    }

    return $data;
  }

  protected function getValues($prefix, $category, $keys)
  {
    if ('enabled' == $category)
    {
      $this->enabled = $this->replaceConstants($keys);
    }
    else if ('loggers' == $category)
    {
      $this->loggers = $this->replaceConstants($keys);

      return array();
    }

    return parent::getValues($prefix, $category, $keys);
  }
}
