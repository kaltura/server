<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Cache class that stores content in a sqlite database.
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfSQLiteCache.class.php 3541 2007-02-26 06:13:12Z fabien $
 */
class sfSQLiteCache extends sfCache
{
  const DEFAULT_NAMESPACE = '';

  protected $conn = null;

 /**
  * File where to put the cache database (or :memory: to store cache in memory)
  */
  protected $database = '';

 /**
  * Disable / Tune the automatic cleaning process
  *
  * The automatic cleaning process destroy too old (for the given life time)
  * cache files when a new cache file is written.
  * 0               => no automatic cache cleaning
  * 1               => systematic cache cleaning
  * x (integer) > 1 => automatic cleaning randomly 1 times on x cache write
  */
  protected $automaticCleaningFactor = 500;

 /**
  * Constructor.
  *
  * @param string The database name
  */
  public function __construct($database = null)
  {
    if (!extension_loaded('sqlite'))
    {
      throw new sfConfigurationException('sfSQLiteCache class needs "sqlite" extension');
    }

    $this->setDatabase($database);
  }

 /**
   * Initializes the cache.
   *
   * @param array An array of options
   * Available options:
   *  - database:                database name
   *  - automaticCleaningFactor: disable / tune automatic cleaning process (int)
   *
   */
  public function initialize($options = array())
  {
    if (isset($options['database']))
    {
      $this->setDatabase($options['database']);
      unset($options['database']);
    }

    $availableOptions = array('automaticCleaningFactor');
    foreach ($options as $key => $value)
    {
      if (!in_array($key, $availableOptions) && sfConfig::get('sf_logging_enabled'))
      {
        sfLogger::getInstance()->err(sprintf('sfSQLiteCache cannot take "%s" as an option', $key));
      }

      $this->$key = $value;
    }
  }

 /**
   * Sets the database name.
   *
   * @param string The database name where to store the cache
   */
  public function setDatabase($database)
  {
    if (!$database)
    {
      return;
    }

    $this->database = $database;

    $new = false;
    if (':memory:' == $database)
    {
      $new = true;
    }
    elseif (!is_file($database))
    {
      $new = true;

      // create cache dir if needed
      $dir = dirname($database);
      $current_umask = umask(0000);
      if (!is_dir($dir))
      {
        @mkdir($dir, 0777, true);
      }

      touch($database);
      umask($current_umask);
    }

    if (!($this->conn = @sqlite_open($this->database, 0644, $errmsg)))
    {
      throw new sfException(sprintf("Unable to connect to SQLite database: %s", $errmsg));
    }

    if ($new)
    {
      $this->createSchema();
    }
  }

 /**
   * Creates the database schema.
   *
   * @throws sfException
   */
  protected function createSchema()
  {
    $statements = array(
      "CREATE TABLE [cache] (
        [id] VARCHAR(255),
        [namespace] VARCHAR(255),
        [data] LONGVARCHAR,
        [created_at] TIMESTAMP
      )",
      "CREATE INDEX [cache_unique] ON [cache] ([namespace], [id])",
    );

    foreach ($statements as $statement)
    {
      if (!sqlite_query($statement, $this->conn))
      {
        throw new sfException(sqlite_error_string(sqlite_last_error($this->database)));
      }
    }
  }

 /**
   * Destructor.
   */
  public function __destruct()
  {
    sqlite_close($this->conn);
  }

 /**
   * Gets the database name.
   *
   * @return string The database name
   */
  public function getDatabase()
  {
    return $this->database;
  }

 /**
  * Tests if a cache is available and (if yes) returns it.
  *
  * @param  string  The cache id
  * @param  string  The name of the cache namespace
  * @param  boolean If set to true, the cache validity won't be tested
  *
  * @return string  The data in the cache (or null if no cache available)
  *
  * @see sfCache
  */
  public function get($id, $namespace = self::DEFAULT_NAMESPACE, $doNotTestCacheValidity = false)
  {
    $statement = sprintf("SELECT data FROM cache WHERE id = '%s' AND namespace = '%s'", sqlite_escape_string($id), sqlite_escape_string($namespace));
    if (!$doNotTestCacheValidity)
    {
      $statement .= sprintf(" AND created_at > '%s'", sqlite_escape_string($this->refreshTime));
    }

    $rs = sqlite_query($statement, $this->conn);

    return sqlite_num_rows($rs) ? sqlite_fetch_single($rs) : null;
  }

  /**
   * Returns true if there is a cache for the given id and namespace.
   *
   * @param  string  The cache id
   * @param  string  The name of the cache namespace
   * @param  boolean If set to true, the cache validity won't be tested
   *
   * @return boolean true if the cache exists, false otherwise
   *
   * @see sfCache
   */
  public function has($id, $namespace = self::DEFAULT_NAMESPACE, $doNotTestCacheValidity = false)
  {
    $statement = sprintf("SELECT id FROM cache WHERE id = '%s' AND namespace = '%s'", sqlite_escape_string($id), sqlite_escape_string($namespace));
    if (!$doNotTestCacheValidity)
    {
      $statement .= sprintf(" AND created_at > '%s'", sqlite_escape_string($this->refreshTime));
    }

    return sqlite_num_rows(sqlite_query($statement, $this->conn)) ? true : false;
  }
  
 /**
  * Saves some data in the cache.
  *
  * @param string The cache id
  * @param string The name of the cache namespace
  * @param string The data to put in cache
  *
  * @return boolean true if no problem
  *
  * @see sfCache
  */
  public function set($id, $namespace = self::DEFAULT_NAMESPACE, $data)
  {
    if ($this->automaticCleaningFactor > 0)
    {
      $rand = rand(1, $this->automaticCleaningFactor);
      if ($rand == 1)
      {
        $this->clean(false, 'old');
      }
    }

    if (!$this->has($id, $namespace))
    {
      $statement = sprintf("INSERT INTO cache (id, namespace, data, created_at) VALUES ('%s', '%s', '%s', %d)", sqlite_escape_string($id), sqlite_escape_string($namespace), sqlite_escape_string($data), time());
    }
    else
    {
      $statement = sprintf("UPDATE cache SET data = '%s', created_at = %s WHERE id = '%s' AND namespace = '%s'", sqlite_escape_string($data), time(), sqlite_escape_string($id), sqlite_escape_string($namespace));
    }

    if (sqlite_query($statement, $this->conn))
    {
      return true;
    }

    return false;
  }

 /**
  * Removes an element from the cache.
  *
  * @param string The cache id
  * @param string The name of the cache namespace
  *
  * @return boolean true if no problem
  *
  * @see sfCache
  */
  public function remove($id, $namespace = self::DEFAULT_NAMESPACE)
  {
    $statement = sprintf("DELETE FROM cache WHERE id = '%s' AND namespace = '%s'", sqlite_escape_string($id), sqlite_escape_string($namespace));
    if (sqlite_query($statement, $this->conn))
    {
      return true;
    }

    return false;
  }

 /**
  * Cleans the cache.
  *
  * If no namespace is specified all cache files will be destroyed
  * else only cache files of the specified namespace will be destroyed.
  *
  * @param string The name of the cache namespace
  *
  * @return boolean true if no problem
  */
  public function clean($namespace = null, $mode = 'all')
  {
    if (!$namespace)
    {
      $statement = "DELETE FROM cache";
    }
    else
    {
      $statement = sprintf("DELETE FROM cache WHERE namespace LIKE '%s%%'", $namespace);
    }

    if ('old' == $mode)
    {
      $statement .= sprintf(" %s created_at < '%s'", $namespace ? 'AND' : 'WHERE', sqlite_escape_string($this->refreshTime));
    }

    return sqlite_num_rows(sqlite_query($statement, $this->conn)) ? true : false;
  }

  public function lastModified($id, $namespace = self::DEFAULT_NAMESPACE)
  {
    $statement = sprintf("SELECT created_at FROM cache WHERE id = '%s' AND namespace = '%s'", sqlite_escape_string($id), sqlite_escape_string($namespace));
    $rs = sqlite_query($statement, $this->conn);

    return sqlite_num_rows($rs) ? intval(sqlite_fetch_single($rs)) : 0;
  }
}
