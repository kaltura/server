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
 * sfDatabase is a base abstraction class that allows you to setup any type of
 * database connection via a configuration file.
 *
 * @package    symfony
 * @subpackage database
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfDatabase.class.php 3210 2007-01-10 20:28:16Z fabien $
 */
abstract class sfDatabase
{
  protected
    $connection      = null,
    $parameterHolder = null,
    $resource        = null;

  /**
   * Connects to the database.
   *
   * @throws <b>sfDatabaseException</b> If a connection could not be created
   */
  abstract function connect();

  /**
   * Retrieves the database connection associated with this sfDatabase implementation.
   *
   * When this is executed on a Database implementation that isn't an
   * abstraction layer, a copy of the resource will be returned.
   *
   * @return mixed A database connection
   *
   * @throws <b>sfDatabaseException</b> If a connection could not be retrieved
   */
  public function getConnection()
  {
    if ($this->connection == null)
    {
      $this->connect();
    }

    return $this->connection;
  }

  /**
   * Retrieves a raw database resource associated with this sfDatabase implementation.
   *
   * @return mixed A database resource
   *
   * @throws <b>sfDatabaseException</b> If a resource could not be retrieved
   */
  public function getResource()
  {
    if ($this->resource == null)
    {
      $this->connect();
    }

    return $this->resource;
  }

  /**
   * Initializes this sfDatabase object.
   *
   * @param array An associative array of initialization parameters
   *
   * @return bool true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this sfDatabase object
   */
  public function initialize($parameters = array())
  {
    $this->parameterHolder = new sfParameterHolder();
    $this->parameterHolder->add($parameters);
  }

  /**
   * Gets the parameter holder for this object.
   *
   * @return sfParameterHolder A sfParameterHolder instance
   */
  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }

  /**
   * Gets the parameter associated with the given key.
   *
   * This is a shortcut for:
   *
   * <code>$this->getParameterHolder()->get()</code>
   *
   * @param string The key name
   * @param string The default value
   * @param string The namespace to use
   *
   * @return string The value associated with the key
   *
   * @see sfParameterHolder
   */
  public function getParameter($name, $default = null, $ns = null)
  {
    return $this->parameterHolder->get($name, $default, $ns);
  }

  /**
   * Returns true if the given key exists in the parameter holder.
   *
   * This is a shortcut for:
   *
   * <code>$this->getParameterHolder()->has()</code>
   *
   * @param string The key name
   * @param string The namespace to use
   *
   * @return boolean true if the given key exists, false otherwise
   *
   * @see sfParameterHolder
   */
  public function hasParameter($name, $ns = null)
  {
    return $this->parameterHolder->has($name, $ns);
  }

  /**
   * Sets the value for the given key.
   *
   * This is a shortcut for:
   *
   * <code>$this->getParameterHolder()->set()</code>
   *
   * @param string The key name
   * @param string The value
   * @param string The namespace to use
   *
   * @see sfParameterHolder
   */
  public function setParameter($name, $value, $ns = null)
  {
    $this->parameterHolder->set($name, $value, $ns);
  }

  /**
   * Executes the shutdown procedure.
   *
   * @return void
   *
   * @throws <b>sfDatabaseException</b> If an error occurs while shutting down this database
   */
  abstract function shutdown();
}
