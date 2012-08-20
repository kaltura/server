<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFactoryConfigHandler allows you to specify which factory implementation the
 * system will use.
 *
 * @package    symfony
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfFactoryConfigHandler.class.php 3203 2007-01-09 18:32:54Z fabien $
 */
class sfFactoryConfigHandler extends sfYamlConfigHandler
{
  /**
   * Executes this configuration handler.
   *
   * @param array An array of absolute filesystem path to a configuration file
   *
   * @return string Data to be written to a cache file
   *
   * @throws <b>sfConfigurationException</b> If a requested configuration file does not exist or is not readable
   * @throws <b>sfParseException</b> If a requested configuration file is improperly formatted
   */
  public function execute($configFiles)
  {
    // parse the yaml
    $myConfig = $this->parseYamls($configFiles);

    $myConfig = sfToolkit::arrayDeepMerge(
      isset($myConfig['default']) && is_array($myConfig['default']) ? $myConfig['default'] : array(),
      isset($myConfig['all']) && is_array($myConfig['all']) ? $myConfig['all'] : array(),
      isset($myConfig[sfConfig::get('sf_environment')]) && is_array($myConfig[sfConfig::get('sf_environment')]) ? $myConfig[sfConfig::get('sf_environment')] : array()
    );

    // init our data and includes arrays
    $includes  = array();
    $inits     = array();
    $instances = array();

    // available list of factories
    $factories = array('controller', 'request', 'response', 'storage', 'user', 'view_cache');

    // let's do our fancy work
    foreach ($factories as $factory)
    {
      // see if the factory exists for this controller
      $keys = $myConfig[$factory];

      if (!isset($keys['class']))
      {
        // missing class key
        $error = sprintf('Configuration file "%s" specifies category "%s" with missing class key', $configFiles[0], $factory);
        throw new sfParseException($error);
      }

      $class = $keys['class'];

      if (isset($keys['file']))
      {
        // we have a file to include
        $file = $this->replaceConstants($keys['file']);
        $file = $this->replacePath($file);

        if (!is_readable($file))
        {
            // factory file doesn't exist
            $error = sprintf('Configuration file "%s" specifies class "%s" with nonexistent or unreadable file "%s"', $configFiles[0], $class, $file);
            throw new sfParseException($error);
        }

        // append our data
        $includes[] = sprintf("require_once('%s');", $file);
      }

      // parse parameters
      if (isset($keys['param']))
      {
        $parameters = array();
        foreach ($keys['param'] as $key => $value)
        {
          $parameters[$key] = $this->replaceConstants($value);
        }
      }
      else
      {
        $parameters = null;
      }
      $parameters = var_export($parameters, true);

      // append new data
      switch ($factory)
      {
        case 'controller':
          // append instance creation
          $instances[] = sprintf("  \$this->controller = sfController::newInstance(sfConfig::get('sf_factory_controller', '%s'));", $class);

          // append instance initialization
          $inits[] = "  \$this->controller->initialize(\$this);";
          break;

        case 'request':
          // append instance creation
          $instances[] = sprintf("  \$this->request = sfRequest::newInstance(sfConfig::get('sf_factory_request', '%s'));", $class);

          // append instance initialization
          $inits[] = sprintf("  \$this->request->initialize(\$this, sfConfig::get('sf_factory_request_parameters', %s), sfConfig::get('sf_factory_request_attributes', array()));", $parameters);
          break;

        case 'response':
          // append instance creation
          $instances[] = sprintf("  \$this->response = sfResponse::newInstance(sfConfig::get('sf_factory_response', '%s'));", $class);

          // append instance initialization
          $inits[] = sprintf("  \$this->response->initialize(\$this, sfConfig::get('sf_factory_response_parameters', %s));", $parameters);
          break;

        case 'storage':
          // append instance creation
          $instances[] = sprintf("  \$this->storage = sfStorage::newInstance(sfConfig::get('sf_factory_storage', '%s'));", $class);

          // append instance initialization
          $inits[] = sprintf("  \$this->storage->initialize(\$this, sfConfig::get('sf_factory_storage_parameters', %s));", $parameters);
          break;

        case 'user':
          // append instance creation
          $instances[] = sprintf("  \$this->user = sfUser::newInstance(sfConfig::get('sf_factory_user', '%s'));", $class);

          // append instance initialization
          $inits[] = sprintf("  \$this->user->initialize(\$this, sfConfig::get('sf_factory_user_parameters', %s));", $parameters);
          break;
        case 'view_cache':
          // append view cache class name
          $inits[] = sprintf("\n  if (sfConfig::get('sf_cache'))\n  {\n".
                             "    \$this->viewCacheManager = new sfViewCacheManager();\n".
                             "    \$this->viewCacheManager->initialize(\$this, sfConfig::get('sf_factory_view_cache', '%s'), sfConfig::get('sf_factory_view_cache_parameters', %s));\n".
                             " }\n",
                             $class, $parameters);
          break;
      }
    }

    // compile data
    $retval = sprintf("<?php\n".
                      "// auto-generated by sfFactoryConfigHandler\n".
                      "// date: %s\n%s\n%s\n%s\n",
                      date('Y/m/d H:i:s'), implode("\n", $includes),
                      implode("\n", $instances), implode("\n", $inits));

    return $retval;
  }
}
