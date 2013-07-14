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
 * sfSessionStorage allows you to store persistent symfony data in the user session.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>auto_start</b>   - [Yes]     - Should session_start() automatically be called?
 * # <b>session_name</b> - [symfony] - The name of the session.
 *
 * @package    symfony
 * @subpackage storage
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfSessionStorage.class.php 3329 2007-01-23 08:29:34Z fabien $
 */
class sfSessionStorage extends sfStorage
{
  /**
   * Initializes this Storage instance.
   *
   * @param sfContext A sfContext instance
   * @param array   An associative array of initialization parameters
   *
   * @return boolean true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this Storage
   */
  public function initialize($context, $parameters = null)
  {
    // initialize parent
    parent::initialize($context, $parameters);

    // set session name
    $sessionName = $this->getParameterHolder()->get('session_name', 'symfony');

    session_name($sessionName);

    $use_cookies = (boolean) ini_get('session.use_cookies');
    if (!$use_cookies)
    {
      $sessionId = $context->getRequest()->getParameter($sessionName, '');

      if ($sessionId != '')
      {
        session_id($sessionId);
      }
    }

    $cookieDefaults = session_get_cookie_params();
    $lifetime = $this->getParameter('session_cookie_lifetime', $cookieDefaults['lifetime']);
    $path     = $this->getParameter('session_cookie_path',     $cookieDefaults['path']);
    $domain   = $this->getParameter('session_cookie_domain',   $cookieDefaults['domain']);
    $secure   = $this->getParameter('session_cookie_secure',   $cookieDefaults['secure']);
    $httpOnly = $this->getParameter('session_cookie_httponly', isset($cookieDefaults['httponly']) ? $cookieDefaults['httponly'] : false);
    if (version_compare(phpversion(), '5.2', '>='))
    {
      session_set_cookie_params($lifetime, $path, $domain, $secure, $httpOnly);
    }
    else
    {
      session_set_cookie_params($lifetime, $path, $domain, $secure);
    }

    if ($this->getParameter('auto_start', true))
    {
      // start our session
      session_start();
    }
  }

  /**
   * Reads data from this storage.
   *
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param string A unique key identifying your data
   *
   * @return mixed Data associated with the key
   */
  public function & read($key)
  {
    $retval = null;

    if (isset($_SESSION[$key]))
    {
      $retval =& $_SESSION[$key];
    }

    return $retval;
  }

  /**
   * Removes data from this storage.
   *
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param string A unique key identifying your data
   *
   * @return mixed Data associated with the key
   */
  public function & remove($key)
  {
    $retval = null;

    if (isset($_SESSION[$key]))
    {
      $retval =& $_SESSION[$key];
      unset($_SESSION[$key]);
    }

    return $retval;
  }

  /**
   * Writes data to this storage.
   *
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param string A unique key identifying your data
   * @param mixed  Data associated with your key
   *
   */
  public function write($key, &$data)
  {
    $_SESSION[$key] =& $data;
  }

  /**
   * Executes the shutdown procedure.
   *
   */
  public function shutdown()
  {
    // don't need a shutdown procedure because read/write do it in real-time
  }
}
