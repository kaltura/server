<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfComponent.
 *
 * @package    symfony
 * @subpackage action
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfComponent.class.php 3379 2007-02-01 06:49:58Z fabien $
 */
abstract class sfComponent
{
  protected
    $context                = null,
    $request                = null,
    $response               = null,
    $varHolder              = null,
    $requestParameterHolder = null;

  /**
   * Execute any application/business logic for this component.
   *
   * In a typical database-driven application, execute() handles application
   * logic itself and then proceeds to create a model instance. Once the model
   * instance is initialized it handles all business logic for the action.
   *
   * A model should represent an entity in your application. This could be a
   * user account, a shopping cart, or even a something as simple as a
   * single product.
   *
   * @return mixed A string containing the view name associated with this action
   */
  abstract function execute();

  /**
   * Gets the module name associated with this component.
   *
   * @return string A module name
   */
  public function getModuleName()
  {
    return $this->getContext()->getModuleName();
  }

  /**
   * Gets the action name associated with this component.
   *
   * @return string An action name
   */
  public function getActionName()
  {
    return $this->getContext()->getActionName();
  }

  /**
   * Initializes this component.
   *
   * @param sfContext The current application context
   *
   * @return boolean true, if initialization completes successfully, otherwise false
   */
  public function initialize($context)
  {
    $this->context                = $context;
    $this->varHolder              = new sfParameterHolder();
    $this->request                = $context->getRequest();
    $this->response               = $context->getResponse();
    $this->requestParameterHolder = $this->request->getParameterHolder();

    return true;
  }

  /**
   * Retrieves the current application context.
   *
   * @return sfContext The current sfContext instance
   */
  public final function getContext()
  {
    return $this->context;
  }

  /**
   * Retrieves the current logger instance.
   *
   * @return sfLogger The current sfLogger instance
   */
  public final function getLogger()
  {
    return $this->context->getLogger();
  }

  /**
   * Logs a message using the sfLogger object.
   *
   * @param mixed  String or object containing the message to log
   * @param string The priority of the message
   *               (available priorities: emerg, alert, crit, err, warning, notice, info, debug)
   *
   * @see sfLogger
   */
  public function logMessage($message, $priority = 'info')
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->context->getLogger()->log($message, constant('SF_LOG_'.strtoupper($priority)));
    }
  }

  /**
   * Displays a message as a short message in the sfWebDebug toolbar.
   *
   * @param string The message text
   *
   * @see sfWebDebug
   */
  public function debugMessage($message)
  {
    if (sfConfig::get('sf_web_debug'))
    {
      sfWebDebug::getInstance()->logShortMessage($message);
    }
  }

  /**
   * Returns the value of a request parameter.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getRequest()->getParameterHolder()->get($name)</code>
   *
   * @param  string The parameter name
   *
   * @return string The request parameter value
   */
  public function getRequestParameter($name, $default = null)
  {
    return $this->requestParameterHolder->get($name, $default);
  }

  /**
   * Returns true if a request parameter exists.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getRequest()->getParameterHolder()->has($name)</code>
   *
   * @param  string  The parameter name
   * @return boolean true if the request parameter exists, false otherwise
   */
  public function hasRequestParameter($name)
  {
    return $this->requestParameterHolder->has($name);
  }

  /**
   * Retrieves the current sfRequest object.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getContext()->getRequest()</code>
   *
   * @return sfRequest The current sfRequest implementation instance
   */
  public function getRequest()
  {
    return $this->request;
  }

  /**
   * Retrieves the current sfResponse object.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getContext()->getResponse()</code>
   *
   * @return sfResponse The current sfResponse implementation instance
   */
  public function getResponse()
  {
    return $this->response;
  }

  /**
   * Retrieves the current sfController object.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getContext()->getController()</code>
   *
   * @return sfController The current sfController implementation instance
   */
  public function getController()
  {
    return $this->getContext()->getController();
  }

  /**
   * Retrieves the current sfUser object.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getContext()->getController()</code>
   *
   * @return sfUser The current sfUser implementation instance
   */
  public function getUser()
  {
    return $this->getContext()->getUser();
  }

  /**
   * Sets a variable for the template.
   *
   * @param  string The variable name
   * @param  mixed  The variable value
   */
  public function setVar($name, $value)
  {
    $this->varHolder->set($name, $value);
  }

  /**
   * Gets a variable set for the template.
   *
   * @param  string The variable name
   * @return mixed  The variable value
   */
  public function getVar($name)
  {
    return $this->varHolder->get($name);
  }

  /**
   * Gets the sfParameterHolder object that stores the template variables.
   *
   * @return sfParameterHolder The variable holder.
   */
  public function getVarHolder()
  {
    return $this->varHolder;
  }

  /**
   * Sets a variable for the template.
   *
   * This is a shortcut for:
   *
   * <code>$this->setVar('name', 'value')</code>
   *
   * @param  string The variable name
   * @param  string The variable value
   *
   * @return boolean always true
   *
   * @see setVar()
   */
  public function __set($key, $value)
  {
    return $this->varHolder->setByRef($key, $value);
  }

  /**
   * Gets a variable for the template.
   *
   * This is a shortcut for:
   *
   * <code>$this->getVar('name')</code>
   *
   * @param  string The variable name
   *
   * @return mixed The variable value
   *
   * @see getVar()
   */
  public function & __get($key)
  {
    return $this->varHolder->get($key);
  }

  /**
   * Returns true if a variable for the template is set.
   *
   * This is a shortcut for:
   *
   * <code>$this->getVarHolder()->has('name')</code>
   *
   * @param  string The variable name
   *
   * @return boolean true if the variable is set
   */
  public function __isset($name)
  {
    return $this->varHolder->has($name);
  }

  /**
   * Removes a variable for the template.
   *
   * This is just really a shortcut for:
   *
   * <code>$this->getVarHolder()->remove('name')</code>
   *
   * @param  string The variable Name
   */
  public function __unset($name)
  {
    $this->varHolder->remove($name);
  }

  /**
   * Sets a flash variable that will be passed to the very next action.
   *
   * @param  string  The name of the flash variable
   * @param  string  The value of the flash variable
   * @param  boolean true if the flash have to persist for the following request (true by default)
   */
  public function setFlash($name, $value, $persist = true)
  {
    $this->getUser()->setAttribute($name, $value, 'symfony/flash');

    if ($persist)
    {
      // clear removal flag
      $this->getUser()->getAttributeHolder()->remove($name, 'symfony/flash/remove');
    }
    else
    {
      $this->getUser()->setAttribute($name, true, 'symfony/flash/remove');
    }
  }

  /**
   * Gets a flash variable.
   *
   * @param  string The name of the flash variable
   *
   * @return mixed The value of the flash variable
   */
  public function getFlash($name)
  {
    return $this->getUser()->getAttribute($name, null, 'symfony/flash');
  }

  /**
   * Returns true if a flash variable of the specified name exists.
   * 
   * @param  string The name of the flash variable
   *
   * @return boolean   true if the variable exists, false otherwise
   */
  public function hasFlash($name)
  {
    return $this->getUser()->hasAttribute($name, 'symfony/flash');
  }

  /**
   * Sends and email from the current action.
   *
   * This methods calls a module/action with the sfMailView class.
   *
   * This is a shortcut for
   *
   * <code>$this->getController()->sendEmail($module, $action)</code>
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
    return $this->getController()->getPresentationFor($module, $action, 'sfMail');
  }

  /**
   * Returns the rendered view presentation of a given module/action.
   *
   * This is a shortcut for
   *
   * <code>$this->getController()->getPresentationFor($module, $action, $viewName)</code>
   *
   * @param  string A module name
   * @param  string An action name
   * @param  string A View class name
   *
   * @return string The generated content
   *
   * @see sfController
   */
  public function getPresentationFor($module, $action, $viewName = null)
  {
    return $this->getController()->getPresentationFor($module, $action, $viewName);
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
    if (!$callable = sfMixer::getCallable('sfComponent:'.$method))
    {
      throw new sfException(sprintf('Call to undefined method sfComponent::%s', $method));
    }

    array_unshift($arguments, $this);

    return call_user_func_array($callable, $arguments);
  }
}
