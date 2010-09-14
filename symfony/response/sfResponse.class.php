<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfResponse provides methods for manipulating client response information such
 * as headers, cookies and content.
 *
 * @package    symfony
 * @subpackage response
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfResponse.class.php 3250 2007-01-12 20:09:11Z fabien $
 */
abstract class sfResponse
{
  protected
    $parameterHolder = null,
    $context         = null,
    $content         = '';

  /**
   * Initializes this sfResponse.
   *
   * @param sfContext A sfContext instance
   *
   * @return boolean true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this Response
   */
  public function initialize($context, $parameters = array())
  {
    $this->context = $context;

    $this->parameterHolder = new sfParameterHolder();
    $this->parameterHolder->add($parameters);
  }

  /**
   * Sets the context for the current response.
   *
   * @param sfContext  A sfContext instance
   */
  public function setContext($context)
  {
    $this->context = $context;
  }

  /**
   * Retrieves the current application context.
   *
   * @return sfContext The application context
   */
  public function getContext()
  {
    return $this->context;
  }

  /**
   * Retrieves a new sfResponse implementation instance.
   *
   * @param string A sfResponse implementation name
   *
   * @return sfResponse A sfResponse implementation instance
   *
   * @throws <b>sfFactoryException</b> If a request implementation instance cannot be created
   */
  public static function newInstance($class)
  {
    // the class exists
    $object = new $class();

    if (!($object instanceof sfResponse))
    {
      // the class name is of the wrong type
      $error = 'Class "%s" is not of the type sfResponse';
      $error = sprintf($error, $class);

      throw new sfFactoryException($error);
    }

    return $object;
  }

  /**
   * Sets the response content
   *
   * @param string Content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }

  /**
   * Gets the current response content
   *
   * @return string Content
   */
  public function getContent()
  {
    return $this->content;
  }

  /**
   * Outputs the response content
   */
  public function sendContent()
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfResponse} send content ('.strlen($this->content).' o)');
    }

    echo $this->content;
  }

  /**
   * Retrieves the parameters from the current response.
   *
   * @return sfParameterHolder List of parameters
   */
  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }

  /**
   * Retrieves a parameter from the current response.
   *
   * @param string A parameter name
   * @param string A default paramter value
   * @param string Namespace for the current response
   *
   * @return mixed A parameter value
   */
  public function getParameter($name, $default = null, $ns = null)
  {
    return $this->parameterHolder->get($name, $default, $ns);
  }

  /**
   * Indicates whether or not a parameter exist for the current response.
   *
   * @param string A parameter name
   * @param string Namespace for the current response
   *
   * @return boolean true, if the parameter exists otherwise false
   */
  public function hasParameter($name, $ns = null)
  {
    return $this->parameterHolder->has($name, $ns);
  }

  /**
   * Sets a parameter for the current response.
   *
   * @param string A parameter name
   * @param string The parameter value to be set
   * @param string Namespace for the current response
   */
  public function setParameter($name, $value, $ns = null)
  {
    $this->parameterHolder->set($name, $value, $ns);
  }

  /**
   * Executes the shutdown procedure.
   *
   */
  abstract function shutdown();

  /**
   * Overloads a given method.
   *
   * @param string Method name
   * @param string Method arguments
   *
   * @return mixed User function callback
   *
   * @throws <b>sfException</b> If the calls fails
   */
  public function __call($method, $arguments)
  {
    if (!$callable = sfMixer::getCallable('sfResponse:'.$method))
    {
      throw new sfException(sprintf('Call to undefined method sfResponse::%s', $method));
    }

    array_unshift($arguments, $this);

    return call_user_func_array($callable, $arguments);
  }
}
