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
 * sfController directs application flow.
 *
 * @package    symfony
 * @subpackage controller
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfController.class.php 3221 2007-01-11 07:33:23Z fabien $
 */
abstract class sfController
{
  protected
    $context                  = null,
    $controllerClasses        = array(),
    $maxForwards              = 5,
    $renderMode               = sfView::RENDER_CLIENT,
    $viewCacheClassName       = null;

  /**
   * Indicates whether or not a module has a specific component.
   *
   * @param string A module name
   * @param string An component name
   *
   * @return bool true, if the component exists, otherwise false
   */
  public function componentExists($moduleName, $componentName)
  {
    return $this->controllerExists($moduleName, $componentName, 'component', false);
  }

  /**
   * Indicates whether or not a module has a specific action.
   *
   * @param string A module name
   * @param string An action name
   *
   * @return bool true, if the action exists, otherwise false
   */
  public function actionExists($moduleName, $actionName)
  {
    return $this->controllerExists($moduleName, $actionName, 'action', false);
  }

  /**
   * Looks for a controller and optionally throw exceptions if existence is required (i.e.
   * in the case of {@link getController()}).
   *
   * @param string  The name of the module
   * @param string  The name of the controller within the module
   * @param string  Either 'action' or 'component' depending on the type of controller to look for
   * @param boolean Whether to throw exceptions if the controller doesn't exist
   *
   * @throws sfConfigurationException thrown if the module is not enabled
   * @throws sfControllerException thrown if the controller doesn't exist and the $throwExceptions parameter is set to true
   *
   * @return boolean true if the controller exists, false otherwise
   */
  protected function controllerExists($moduleName, $controllerName, $extension, $throwExceptions)
  {
    $dirs = sfLoader::getControllerDirs($moduleName);
    foreach ($dirs as $dir => $checkEnabled)
    {
      // plugin module enabled?
      if ($checkEnabled && !in_array($moduleName, sfConfig::get('sf_enabled_modules')) && is_readable($dir))
      {
        $error = 'The module "%s" is not enabled.';
        $error = sprintf($error, $moduleName);

        throw new sfConfigurationException($error);
      }

      // one action per file or one file for all actions
      $classFile   = strtolower($extension);
      $classSuffix = ucfirst(strtolower($extension));
      $file        = $dir.'/'.$controllerName.$classSuffix.'.class.php';
      if (is_readable($file))
      {
        // action class exists
        require_once($file);

        $this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix] = $controllerName.$classSuffix;

        return true;
      }

      $module_file = $dir.'/'.$classFile.'s.class.php';
      if (is_readable($module_file))
      {
        // module class exists
        require_once($module_file);

        if (!class_exists($moduleName.$classSuffix.'s', false))
        {
          if ($throwExceptions)
          {
            throw new sfControllerException(sprintf('There is no "%s" class in your action file "%s".', $moduleName.$classSuffix.'s', $module_file));
          }

          return false;
        }

        // action is defined in this class?
        if (!in_array('execute'.ucfirst($controllerName), get_class_methods($moduleName.$classSuffix.'s')))
        {
          if ($throwExceptions)
          {
            throw new sfControllerException(sprintf('There is no "%s" method in your action class "%s"', 'execute'.ucfirst($controllerName), $moduleName.$classSuffix.'s'));
          }

          return false;
        }

        $this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix] = $moduleName.$classSuffix.'s';
        return true;
      }
    }

    // send an exception if debug
    if ($throwExceptions && sfConfig::get('sf_debug'))
    {
      $dirs = array_keys($dirs);

      // remove sf_root_dir from dirs
      foreach ($dirs as &$dir)
      {
        $dir = str_replace(sfConfig::get('sf_root_dir'), '%SF_ROOT_DIR%', $dir);
      }

      throw new sfControllerException(sprintf('{sfController} controller "%s/%s" does not exist in: %s', $moduleName, $controllerName, implode(', ', $dirs)));
    }

    return false;
  }

  /**
   * Forwards the request to another action.
   *
   * @param string  A module name
   * @param string  An action name
   *
   * @throws <b>sfConfigurationException</b> If an invalid configuration setting has been found
   * @throws <b>sfForwardException</b> If an error occurs while forwarding the request
   * @throws <b>sfInitializationException</b> If the action could not be initialized
   * @throws <b>sfSecurityException</b> If the action requires security but the user implementation is not of type sfSecurityUser
   */
  public function forward($moduleName, $actionName)
  {
    // replace unwanted characters
    $moduleName = preg_replace('/[^a-z0-9\-_]+/i', '', $moduleName);
    $actionName = preg_replace('/[^a-z0-9\-_]+/i', '', $actionName);

    if ($this->getActionStack()->getSize() >= $this->maxForwards)
    {
      // let's kill this party before it turns into cpu cycle hell
      $error = 'Too many forwards have been detected for this request (> %d)';
      $error = sprintf($error, $this->maxForwards);

      throw new sfForwardException($error);
    }

    $rootDir = sfConfig::get('sf_root_dir');
    $app     = sfConfig::get('sf_app');
    $env     = sfConfig::get('sf_environment');

    if (!sfConfig::get('sf_available') || sfToolkit::hasLockFile($rootDir.'/'.$app.'_'.$env.'.clilock'))
    {
      // application is unavailable
      $moduleName = sfConfig::get('sf_unavailable_module');
      $actionName = sfConfig::get('sf_unavailable_action');

      if (!$this->actionExists($moduleName, $actionName))
      {
        // cannot find unavailable module/action
        $error = 'Invalid configuration settings: [sf_unavailable_module] "%s", [sf_unavailable_action] "%s"';
        $error = sprintf($error, $moduleName, $actionName);

        throw new sfConfigurationException($error);
      }
    }

    // check for a module generator config file
    sfConfigCache::getInstance()->import(sfConfig::get('sf_app_module_dir_name').'/'.$moduleName.'/'.sfConfig::get('sf_app_module_config_dir_name').'/generator.yml', true, true);

    if (!$this->actionExists($moduleName, $actionName))
    {
      // the requested action doesn't exist
      if (sfConfig::get('sf_logging_enabled'))
      {
        $this->getContext()->getLogger()->info('{sfController} action does not exist');
      }

      // track the requested module so we have access to the data in the error 404 page
      $this->context->getRequest()->setAttribute('requested_action', $actionName);
      $this->context->getRequest()->setAttribute('requested_module', $moduleName);

      // switch to error 404 action
      $moduleName = sfConfig::get('sf_error_404_module');
      $actionName = sfConfig::get('sf_error_404_action');

      if (!$this->actionExists($moduleName, $actionName))
      {
        // cannot find unavailable module/action
        $error = 'Invalid configuration settings: [sf_error_404_module] "%s", [sf_error_404_action] "%s"';
        $error = sprintf($error, $moduleName, $actionName);

        throw new sfConfigurationException($error);
      }
    }

    // create an instance of the action
    $actionInstance = $this->getAction($moduleName, $actionName);

    // add a new action stack entry
    $this->getActionStack()->addEntry($moduleName, $actionName, $actionInstance);

    // include module configuration
    require(sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_app_module_dir_name').'/'.$moduleName.'/'.sfConfig::get('sf_app_module_config_dir_name').'/module.yml'));

    // check if this module is internal
    if ($this->getActionStack()->getSize() == 1 && sfConfig::get('mod_'.strtolower($moduleName).'_is_internal') && !sfConfig::get('sf_test'))
    {
      $error = 'Action "%s" from module "%s" cannot be called directly';
      $error = sprintf($error, $actionName, $moduleName);

      throw new sfConfigurationException($error);
    }

    if (sfConfig::get('mod_'.strtolower($moduleName).'_enabled'))
    {
      // module is enabled

      // check for a module config.php
      $moduleConfig = sfConfig::get('sf_app_module_dir').'/'.$moduleName.'/'.sfConfig::get('sf_app_module_config_dir_name').'/config.php';
      if (is_readable($moduleConfig))
      {
        require_once($moduleConfig);
      }

      // initialize the action
      if ($actionInstance->initialize($this->context))
      {
        // create a new filter chain
        $filterChain = new sfFilterChain();
        $this->loadFilters($filterChain, $actionInstance);

        if ($moduleName == sfConfig::get('sf_error_404_module') && $actionName == sfConfig::get('sf_error_404_action'))
        {
          $this->getContext()->getResponse()->setStatusCode(404);
          $this->getContext()->getResponse()->setHttpHeader('Status', '404 Not Found');

          foreach (sfMixer::getCallables('sfController:forward:error404') as $callable)
          {
            call_user_func($callable, $this, $moduleName, $actionName);
          }
        }

        // change i18n message source directory to our module
        if (sfConfig::get('sf_i18n'))
        {
          $this->context->getI18N()->setMessageSourceDir(sfLoader::getI18NDir($moduleName), $this->context->getUser()->getCulture());
        }

        // process the filter chain
        $filterChain->execute();
      }
      else
      {
        // action failed to initialize
        $error = 'Action initialization failed for module "%s", action "%s"';
        $error = sprintf($error, $moduleName, $actionName);

        throw new sfInitializationException($error);
      }
    }
    else
    {
      // module is disabled
      $moduleName = sfConfig::get('sf_module_disabled_module');
      $actionName = sfConfig::get('sf_module_disabled_action');

      if (!$this->actionExists($moduleName, $actionName))
      {
        // cannot find mod disabled module/action
        $error = 'Invalid configuration settings: [sf_module_disabled_module] "%s", [sf_module_disabled_action] "%s"';
        $error = sprintf($error, $moduleName, $actionName);

        throw new sfConfigurationException($error);
      }

      $this->forward($moduleName, $actionName);
    }
  }

  /**
   * Retrieves an sfAction implementation instance.
   *
   * @param  string A module name
   * @param  string An action name
   *
   * @return sfAction An sfAction implementation instance, if the action exists, otherwise null
   */
  public function getAction($moduleName, $actionName)
  {
    return $this->getController($moduleName, $actionName, 'action');
  }

  /**
   * Retrieves a sfComponent implementation instance.
   *
   * @param  string A module name
   * @param  string A component name
   *
   * @return sfComponent A sfComponent implementation instance, if the component exists, otherwise null
   */
  public function getComponent($moduleName, $componentName)
  {
    return $this->getController($moduleName, $componentName, 'component');
  }

  /**
   * Retrieves a controller implementation instance.
   *
   * @param  string A module name
   * @param  string A component name
   * @param  string  Either 'action' or 'component' depending on the type of controller to look for
   *
   * @return object A controller implementation instance, if the controller exists, otherwise null
   *
   * @see getComponent(), getAction()
   */
  protected function getController($moduleName, $controllerName, $extension)
  {
    $classSuffix = ucfirst(strtolower($extension));
    if (!isset($this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix]))
    {
      $this->controllerExists($moduleName, $controllerName, $extension, true);
    }

    $class = $this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix];

    // fix for same name classes
    $moduleClass = $moduleName.'_'.$class;

    if (class_exists($moduleClass, false))
    {
      $class = $moduleClass;
    }

    return new $class();
  }

  /**
   * Retrieves the action stack.
   *
   * @return sfActionStack An sfActionStack instance, if the action stack is enabled, otherwise null
   */
  public function getActionStack()
  {
    return $this->context->getActionStack();
  }

  /**
   * Retrieves the current application context.
   *
   * @return sfContext A sfContext instance
   */
  public function getContext()
  {
    return $this->context;
  }

  /**
   * Retrieves the presentation rendering mode.
   *
   * @return int One of the following:
   *             - sfView::RENDER_CLIENT
   *             - sfView::RENDER_VAR
   */
  public function getRenderMode()
  {
    return $this->renderMode;
  }

  /**
   * Retrieves a sfView implementation instance.
   *
   * @param string A module name
   * @param string An action name
   * @param string A view name
   *
   * @return sfView A sfView implementation instance, if the view exists, otherwise null
   */
  public function getView($moduleName, $actionName, $viewName)
  {
    // user view exists?
    $file = sfConfig::get('sf_app_module_dir').'/'.$moduleName.'/'.sfConfig::get('sf_app_module_view_dir_name').'/'.$actionName.$viewName.'View.class.php';

    if (is_readable($file))
    {
      require_once($file);

      $class = $actionName.$viewName.'View';

      // fix for same name classes
      $moduleClass = $moduleName.'_'.$class;

      if (class_exists($moduleClass, false))
      {
        $class = $moduleClass;
      }
    }
    else
    {
      // view class (as configured in module.yml or defined in action)
      $viewName = $this->getContext()->getRequest()->getAttribute($moduleName.'_'.$actionName.'_view_name', sfConfig::get('mod_'.strtolower($moduleName).'_view_class'), 'symfony/action/view');
      $class    = sfCore::getClassPath($viewName.'View') ? $viewName.'View' : 'sfPHPView';
    }

    return new $class();
  }

  /**
   * Initializes this controller.
   *
   * @param sfContext A sfContext implementation instance
   */
  public function initialize($context)
  {
    $this->context = $context;

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->context->getLogger()->info('{sfController} initialization');
    }

    // set max forwards
    $this->maxForwards = sfConfig::get('sf_max_forwards');
  }

  /**
   * Retrieves a new sfController implementation instance.
   *
   * @param string A sfController class name
   *
   * @return sfController A sfController implementation instance
   *
   * @throws sfFactoryException If a new controller implementation instance cannot be created
   */
  public static function newInstance($class)
  {
    try
    {
      // the class exists
      $object = new $class();

      if (!($object instanceof sfController))
      {
          // the class name is of the wrong type
          $error = 'Class "%s" is not of the type sfController';
          $error = sprintf($error, $class);

          throw new sfFactoryException($error);
      }

      return $object;
    }
    catch (sfException $e)
    {
      $e->printStackTrace();
    }
  }

  /**
   * Sends and email from the current action.
   *
   * This methods calls a module/action with the sfMailView class.
   *
   * @param  string A module name
   * @param  string An action name
   *
   * @return string The generated mail content
   *
   * @see sfMailView, getPresentationFor(), sfController
   */
  public function sendEmail($module, $action)
  {
    return $this->getPresentationFor($module, $action, 'sfMail');
  }

  /**
   * Returns the rendered view presentation of a given module/action.
   *
   * @param  string A module name
   * @param  string An action name
   * @param  string A View class name
   *
   * @return string The generated content
   */
  public function getPresentationFor($module, $action, $viewName = null)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfController} get presentation for action "'.$module.'/'.$action.'" (view class: "'.$viewName.'")');
    }

    // get original render mode
    $renderMode = $this->getRenderMode();

    // set render mode to var
    $this->setRenderMode(sfView::RENDER_VAR);

    // grab the action stack
    $actionStack = $this->getActionStack();

    // grab this next forward's action stack index
    $index = $actionStack->getSize();

    // set viewName if needed
    if ($viewName)
    {
      $this->getContext()->getRequest()->setAttribute($module.'_'.$action.'_view_name', $viewName, 'symfony/action/view');
    }

    // forward to the mail action
    $this->forward($module, $action);

    // grab the action entry from this forward
    $actionEntry = $actionStack->getEntry($index);

    // get raw email content
    $presentation =& $actionEntry->getPresentation();

    // put render mode back
    $this->setRenderMode($renderMode);

    // remove the action entry
    $nb = $actionStack->getSize() - $index;
    while ($nb-- > 0)
    {
      $actionEntry = $actionStack->popEntry();

      if ($actionEntry->getModuleName() == sfConfig::get('sf_login_module') && $actionEntry->getActionName() == sfConfig::get('sf_login_action'))
      {
        $error = 'Your mail action is secured but the user is not authenticated.';

        throw new sfException($error);
      }
      else if ($actionEntry->getModuleName() == sfConfig::get('sf_secure_module') && $actionEntry->getActionName() == sfConfig::get('sf_secure_action'))
      {
        $error = 'Your mail action is secured but the user does not have access.';

        throw new sfException($error);
      }
    }

    // remove viewName
    if ($viewName)
    {
      $this->getContext()->getRequest()->getAttributeHolder()->remove($module.'_'.$action.'_view_name', 'symfony/action/view');
    }

    return $presentation;
  }

  /**
   * Sets the presentation rendering mode.
   *
   * @param int A rendering mode
   *
   * @throws sfRenderException If an invalid render mode has been set
   */
  public function setRenderMode($mode)
  {
    if ($mode == sfView::RENDER_CLIENT || $mode == sfView::RENDER_VAR || $mode == sfView::RENDER_NONE)
    {
      $this->renderMode = $mode;

      return;
    }

    // invalid rendering mode type
    $error = 'Invalid rendering mode: %s';
    $error = sprintf($error, $mode);

    throw new sfRenderException($error);
  }

  /**
   * Indicates whether or not we were called using the CLI version of PHP.
   *
   * @return bool true, if using cli, otherwise false.
   */
  public function inCLI()
  {
    return 0 == strncasecmp(PHP_SAPI, 'cli', 3);
  }

  /**
   * Loads application nad module filters.
   *
   * @param sfFilterChain A sfFilterChain instance
   * @param sfAction      A sfAction instance
   */
  public function loadFilters($filterChain, $actionInstance)
  {
    $moduleName = $this->context->getModuleName();

    require(sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_app_module_dir_name').'/'.$moduleName.'/'.sfConfig::get('sf_app_module_config_dir_name').'/filters.yml'));
  }

  /**
   * Calls methods defined via the sfMixer class.
   *
   * @param string The method name
   * @param array  The method arguments
   *
   * @return mixed The returned value of the called method
   *
   * @see sfMixer
   */
  public function __call($method, $arguments)
  {
    if (!$callable = sfMixer::getCallable('sfController:'.$method))
    {
      throw new sfException(sprintf('Call to undefined method sfController::%s', $method));
    }

    array_unshift($arguments, $this);

    return call_user_func_array($callable, $arguments);
  }
}
