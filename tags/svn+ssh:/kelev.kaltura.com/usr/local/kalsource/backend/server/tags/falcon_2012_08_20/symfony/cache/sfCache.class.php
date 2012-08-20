<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfCache is an abstract class for all cache classes in symfony.
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Fabien Marty <fab@php.net>
 * @version    SVN: $Id: sfCache.class.php 3198 2007-01-08 20:36:20Z fabien $
 */
abstract class sfCache
{
 /**
  * Cache lifetime (in seconds)
  *
  * @var int $lifeTime
  */
  protected $lifeTime = 86400;

 /**
  * Timestamp of the last valid cache
  *
  * @var int $refreshTime
  */
  protected $refreshTime;

 /**
  * Gets the cache content for a given id and namespace.
  *
  * @param  string  The cache id
  * @param  string  The name of the cache namespace
  * @param  boolean If set to true, the cache validity won't be tested
  *
  * @return string  The data of the cache (or null if no cache available)
  */
  abstract public function get($id, $namespace = self::DEFAULT_NAMESPACE, $doNotTestCacheValidity = false);

  /**
   * Returns true if there is a cache for the given id and namespace.
   *
   * @param  string  The cache id
   * @param  string  The name of the cache namespace
   * @param  boolean If set to true, the cache validity won't be tested
   *
   * @return boolean true if the cache exists, false otherwise
   */
  abstract public function has($id, $namespace = self::DEFAULT_NAMESPACE, $doNotTestCacheValidity = false);

 /**
  * Saves some data in the cache.
  *
  * @param string The cache id
  * @param string The name of the cache namespace
  * @param string The data to put in cache
  *
  * @return boolean true if no problem
  */
  abstract public function set($id, $namespace = self::DEFAULT_NAMESPACE, $data);

 /**
  * Removes a content from the cache.
  *
  * @param string The cache id
  * @param string The name of the cache namespace
  *
  * @return boolean true if no problem
  */
  abstract public function remove($id, $namespace = self::DEFAULT_NAMESPACE);

 /**
  * Cleans the cache.
  *
  * If no namespace is specified all cache content will be destroyed
  * else only cache contents of the specified namespace will be destroyed.
  *
  * @param string The name of the cache namespace
  *
  * @return boolean true if no problem
  */
  abstract public function clean($namespace = null, $mode = 'all');

 /**
  * Sets a new life time.
  *
  * @param int The new life time (in seconds)
  */
  public function setLifeTime($newLifeTime)
  {
    $this->lifeTime = $newLifeTime;
    $this->refreshTime = time() - $newLifeTime;
  }

  /**
   * Returns the current life time.
   *
   * @return int The current life time (in seconds)
   */
  public function getLifeTime()
  {
    return $this->lifeTime;
  }

 /**
  * Returns the cache last modification time.
  *
  * @return int The last modification time
  */
  abstract public function lastModified($id, $namespace = self::DEFAULT_NAMESPACE);
}
