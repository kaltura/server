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
 * sfAction executes all the logic for the current request.
 *
 * @package    symfony
 * @subpackage action
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfAction.class.php 3624 2007-03-17 10:57:03Z fabien $
 */
abstract class sfAction extends sfComponent
{
  protected
    $security = array();

  /**
   * Initializes this action.
   *
   * @param sfContext The current application context.
   *
   * @return bool true, if initialization completes successfully, otherwise false
   */
  public function initialize($context)
  {
    parent::initialize($context);

    // include security configuration
    require(sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_app_module_dir_name').'/'.$this->getModuleName().'/'.sfConfig::get('sf_app_module_config_dir_name').'/security.yml', true));

    return true;
  }

  /**
   * Executes an application defined process prior to execution of this sfAction object.
   *
   * By default, this method is empty.
   */
  public function preExecute()
  {
  }

  /**
   * Execute an application defined process immediately after execution of this sfAction object.
   *
   * By default, this method is empty.
   */
  public function postExecute()
  {
  }

  /**
   * Forwards current action to the default 404 error action.
   *
   * @param  string Message of the generated exception
   *
   * @throws sfError404Exception
   *
   */
  public function forward404($message = '')
  {
    throw new sfError404Exception($message);
  }

  /**
   * Forwards current action to the default 404 error action unless the specified condition is true.
   *
   * @param bool A condition that evaluates to true or false
   * @param  string Message of the generated exception
   *
   * @throws sfError404Exception
   */
  public function forward404Unless($condition, $message = '')
  {
    if (!$condition)
    {
      throw new sfError404Exception($message);
    }
  }

  /**
   * Forwards current action to the default 404 error action if the specified condition is true.
   *
   * @param bool A condition that evaluates to true or false
   * @param  string Message of the generated exception
   *
   * @throws sfError404Exception
   */
  public function forward404If($condition, $message = '')
  {
    if ($condition)
    {
      throw new sfError404Exception($message);
    }
  }

  /**
   * Redirects current action to the default 404 error action (with browser redirection).
   *
   * This method stops the current code flow.
   *
   */
  public function redirect404()
  {
    return $this->redirect('/'.sfConfig::get('sf_error_404_module').'/'.sfConfig::get('sf_error_404_action'));
  }

  /**
   * Forwards current action to a new one (without browser redirection).
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  string A module name
   * @param  string An action name
   *
   * @throws sfStopException
   */
  public function forward($module, $action)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfAction} forward to action "'.$module.'/'.$action.'"');
    }

    $this->getController()->forward($module, $action);

    throw new sfStopException();
  }

  /**
   * If the condition is true, forwards current action to a new one (without browser redirection).
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  bool   A condition that evaluates to true or false
   * @param  string A module name
   * @param  string An action name
   *
   * @throws sfStopException
   */
  public function forwardIf($condition, $module, $action)
  {
    if ($condition)
    {
      $this->forward($module, $action);
    }
  }

  /**
   * Unless the condition is true, forwards current action to a new one (without browser redirection).
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  bool   A condition that evaluates to true or false
   * @param  string A module name
   * @param  string An action name
   *
   * @throws sfStopException
   */
  public function forwardUnless($condition, $module, $action)
  {
    if (!$condition)
    {
      $this->forward($module, $action);
    }
  }

  /**
   * Redirects current request to a new URL.
   *
   * 2 URL formats are accepted :
   *  - a full URL: http://www.google.com/
   *  - an internal URL (url_for() format): module/action
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  string Url
   * @param  string Status code (default to 302)
   *
   * @throws sfStopException
   */
  public function redirect($url, $statusCode = 302)
  {
    $url = $this->getController()->genUrl($url, true);

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfAction} redirect to "'.$url.'"');
    }

    $this->getController()->redirect($url, 0, $statusCode);

    throw new sfStopException();
  }

  /**
   * Redirects current request to a new URL, only if specified condition is true.
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  bool   A condition that evaluates to true or false
   * @param  string url
   *
   * @throws sfStopException
   *
   * @see redirect
   */
  public function redirectIf($condition, $url)
  {
    if ($condition)
    {
      $this->redirect($url);
    }
  }

  /**
   * Redirects current request to a new URL, unless specified condition is true.
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  bool   A condition that evaluates to true or false
   * @param  string Url
   *
   * @throws sfStopException
   *
   * @see redirect
   */
  public function redirectUnless($condition, $url)
  {
    if (!$condition)
    {
      $this->redirect($url);
    }
  }

  /**
   * Appends the given text to the response content and bypasses the built-in view system.
   *
   * This method must be called as with a return:
   *
   * <code>return $this->renderText('some text')</code>
   *
   * @param  string Text to append to the response
   *
   * @return sfView::NONE
   */
  public function renderText($text)
  {
    $this->getResponse()->setContent($this->getResponse()->getContent().$text);

    return sfView::NONE;
  }

  /**
   * Retrieves the default view to be executed when a given request is not served by this action.
   *
   * @return string A string containing the view name associated with this action
   */
  public function getDefaultView()
  {
    return sfView::INPUT;
  }

  /**
   * Retrieves the request methods on which this action will process validation and execution.
   *
   * @return int One of the following values:
   *
   * - sfRequest::GET
   * - sfRequest::POST
   * - sfRequest::PUT
   * - sfRequest::DELETE
   * - sfRequest::HEAD
   * - sfRequest::NONE
   *
   * @see sfRequest
   */
  public function getRequestMethods()
  {
    return sfRequest::GET
           | sfRequest::POST
           | sfRequest::PUT
           | sfRequest::DELETE
           | sfRequest::HEAD 
           | sfRequest::NONE;
  }

  /**
   * Executes any post-validation error application logic.
   *
   * @return string A string containing the view name associated with this action
   */
  public function handleError()
  {
    return sfView::ERROR;
  }

  /**
   * Validates manually files and parameters.
   *
   * @return bool true, if validation completes successfully, otherwise false.
   */
  public function validate()
  {
    return true;
  }

  /**
   * Returns the security configuration for this module.
   *
   * @return string Current security configuration as an array
   */
  public function getSecurityConfiguration()
  {
    return $this->security;
  }

  /**
   * Overrides the current security configuration for this module.
   *
   * @param array The new security configuration
   */
  public function setSecurityConfiguration($security)
  {
    $this->security = $security;
  }

  /**
   * Indicates that this action requires security.
   *
   * @return bool true, if this action requires security, otherwise false.
   */
  public function isSecure()
  {
    $actionName = strtolower($this->getActionName());

    if (isset($this->security[$actionName]['is_secure']))
    {
      return $this->security[$actionName]['is_secure'];
    }

    if (isset($this->security['all']['is_secure']))
    {
      return $this->security['all']['is_secure'];
    }

    return false;
  }

  /**
   * Gets credentials the user must have to access this action.
   *
   * @return mixed An array or a string describing the credentials the user must have to access this action
   */
  public function getCredential()
  {
    $actionName = strtolower($this->getActionName());

    if (isset($this->security[$actionName]['credentials']))
    {
      $credentials = $this->security[$actionName]['credentials'];
    }
    else if (isset($this->security['all']['credentials']))
    {
      $credentials = $this->security['all']['credentials'];
    }
    else
    {
      $credentials = null;
    }

    return $credentials;
  }

  /**
   * Sets an alternate template for this sfAction.
   *
   * See 'Naming Conventions' in the 'Symfony View' documentation.
   *
   * @param string Template name
   */
  public function setTemplate($name)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfAction} change template to "'.$name.'"');
    }

    $this->getResponse()->setParameter($this->getModuleName().'_'.$this->getActionName().'_template', $name, 'symfony/action/view');
  }

  /**
   * Gets the name of the alternate template for this sfAction.
   *
   * WARNING: It only returns the template you set with the setTemplate() method,
   *          and does not return the template that you configured in your view.yml.
   *
   * See 'Naming Conventions' in the 'Symfony View' documentation.
   *
   * @return string Template name. Returns null if no template has been set within the action
   */
  public function getTemplate()
  {
    return $this->getResponse()->getParameter($this->getModuleName().'_'.$this->getActionName().'_template', null, 'symfony/action/view');
  }

  /**
   * Sets an alternate layout for this sfAction.
   *
   * To de-activate the layout, set the layout name to false.
   *
   * To revert the layout to the one configured in the view.yml, set the template name to null.
   *
   * @param mixed Layout name or false to de-activate the layout
   */
  public function setLayout($name)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfAction} change layout to "'.$name.'"');
    }

    $this->getResponse()->setParameter($this->getModuleName().'_'.$this->getActionName().'_layout', $name, 'symfony/action/view');
  }

  /**
   * Gets the name of the alternate layout for this sfAction.
   *
   * WARNING: It only returns the layout you set with the setLayout() method,
   *          and does not return the layout that you configured in your view.yml.
   *
   * @return mixed Layout name. Returns null if no layout has been set within the action
   */
  public function getLayout()
  {
    return $this->getResponse()->getParameter($this->getModuleName().'_'.$this->getActionName().'_layout', null, 'symfony/action/view');
  }

  /**
   * Changes the default view class used for rendering the template associated with the current action.
   *
   * @param string View class name
   */
  public function setViewClass($class)
  {
    sfConfig::set('mod_'.strtolower($this->getModuleName()).'_view_class', $class);
  }
}
