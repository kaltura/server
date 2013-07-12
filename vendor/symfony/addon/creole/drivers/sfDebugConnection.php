<?php

/**
 * Debug implementation of Connection.
 *
 * This is a Connection that implements the decorator pattern, wrapping around
 * the true Connection object (stored in $childConnection). This Connection
 * tracks information about queries executed and makes that information available
 * for debugging purposes. The information tracked is the last query executed
 * on the connection (getLastExecutedQuery()) and the total number of
 * queries executed on the connection thus far (getNumQueriesExecuted()).
 *
 * To use this debug connection, you need to register it as a new Creole
 * driver that handles all connection types. To do this, call the following
 * before calling Creole::getConnection():
 *
 * <code>
 * Creole::registerDriver('*', 'creole.drivers.debug.DebugConnection');
 * </code>
 *
 * The next call to Creole::getConnection() will return an instance of
 * DebugConnection.
 *
 * @author Michael Sims
 * @package creole.drivers.debug
 */
class sfDebugConnection implements Connection
{
  /** @var Connection */
  private $childConnection = null;

  /** @var int */
  private $numQueriesExecuted = 0;

  /** @var string */
  private $lastExecutedQuery = '';

  /**
   * Optional PEAR Log class; if set queries will be logged at PEAR_LOG_INFO level.
   * @var Log
   */
  private static $logger;

  /**
   * Sets a Logger class (e.g. PEAR Log) to use for logging.
   * The logger class must have a log() method.  All messages are logged at default log level.
   * @param object $logger
   */
  public static function setLogger($logger)
  {
    self::$logger = $logger;
  }

  /**
   * Returns the number of queries executed on this connection so far
   *
   * @return int
   */
  public function getNumQueriesExecuted()
  {
    return $this->numQueriesExecuted;
  }

  /**
   * Returns the last query executed on this connection
   *
   * @return string
   */
  public function getLastExecutedQuery()
  {
    return $this->lastExecutedQuery;
  }

  /**
   * connect()
   */
  public function connect($dsninfo, $flags = 0)
  {
    if (!($driver = Creole::getDriver($dsninfo['phptype'])))
    {
      throw new SQLException("No driver has been registered to handle connection type: $type");
    }
    $connectionClass = Creole::import($driver);
    $this->childConnection = new $connectionClass();
    $this->log("{sfCreole} connect(): DSN: ". var_export($dsninfo, true) . ", FLAGS: " . var_export($flags, true));
    return $this->childConnection->connect($dsninfo, $flags);
  }

  /**
   * @see Connection::getDatabaseInfo()
   */
  public function getDatabaseInfo()
  {
    return $this->childConnection->getDatabaseInfo();
  }

  /**
   * @see Connection::getIdGenerator()
   */
  public function getIdGenerator()
  {
    return $this->childConnection->getIdGenerator();
  }

  /**
   * @see Connection::isConnected()
   */
  public function isConnected()
  {
    return $this->childConnection->isConnected();
  }

  /**
   * @see Connection::prepareStatement()
   */
  public function prepareStatement($sql)
  {
    $this->log("{sfCreole} prepareStatement(): $sql");
    $obj = $this->childConnection->prepareStatement($sql);
    $objClass = get_class($obj);
    return new $objClass($this, $sql);
  }

  /**
   * @see Connection::createStatement()
   */
  public function createStatement()
  {
    $obj = $this->childConnection->createStatement();
    $objClass = get_class($obj);
    return new $objClass($this);
  }

  /**
   * @see Connection::applyLimit()
   */
  public function applyLimit(&$sql, $offset, $limit)
  {
    $this->log("{sfCreole} applyLimit(): $sql, offset: $offset, limit: $limit");
    return $this->childConnection->applyLimit($sql, $offset, $limit);
  }

  /**
   * @see Connection::close()
   */
  public function close()
  {
    $this->log("{sfCreole} close(): Closing connection.");
    return $this->childConnection->close();
  }

  /**
   * @see Connection::executeQuery()
   */
  public function executeQuery($sql, $fetchmode = null)
  {
    $this->lastExecutedQuery = $sql;
    $this->numQueriesExecuted++;

    $elapsedTime = 0;
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $sqlTimer = sfTimerManager::getTimer('Database');
      $timer = new sfTimer();
    }

    $retval = $this->childConnection->executeQuery($sql, $fetchmode);

    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $sqlTimer->addTime();
      $elapsedTime = $timer->getElapsedTime();
    }

    $this->log(sprintf("{sfCreole} executeQuery(): [%.2f ms] %s", $elapsedTime * 1000, $sql));

    return $retval;
  }

  /**
  * @see Connection::executeUpdate()
  **/
  public function executeUpdate($sql)
  {
    $this->log("{sfCreole} executeUpdate(): $sql");
    $this->lastExecutedQuery = $sql;
    $this->numQueriesExecuted++;
    return $this->childConnection->executeUpdate($sql);
  }

  /**
   * @see Connection::getUpdateCount()
   */
  public function getUpdateCount()
  {
    return $this->childConnection->getUpdateCount();
  }

  /**
   * @see Connection::prepareCall()
   **/
  public function prepareCall($sql)
  {
    $this->log("{sfCreole} prepareCall(): $sql");
    return $this->childConnection->prepareCall($sql);
  }

  /**
   * @see Connection::getResource()
   */
  public function getResource()
  {
    return $this->childConnection->getResource();
  }

  /**
   * @see Connection::connect()
   */
  public function getDSN()
  {
    return $this->childConnection->getDSN();
  }

  /**
   * @see Connection::getFlags()
   */
  public function getFlags()
  {
    return $this->childConnection->getFlags();
  }

  /**
   * @see Connection::begin()
   */
  public function begin()
  {
    $this->log("{sfCreole} beginning transaction.");
    return $this->childConnection->begin();
  }

  /**
   * @see Connection::commit()
   */
  public function commit()
  {
    $this->log("{sfCreole} committing transaction.");
    return $this->childConnection->commit();
  }

  /**
   * @see Connection::rollback()
   */
  public function rollback()
  {
    $this->log("{sfCreole} rolling back transaction.");
    return $this->childConnection->rollback();
  }

  /**
   * @see Connection::setAutoCommit()
   */
  public function setAutoCommit($bit)
  {
    $this->log("{sfCreole} setting autocommit to: ".var_export($bit, true));
    return $this->childConnection->setAutoCommit($bit);
  }

  /**
   * @see Connection::getAutoCommit()
   */
  public function getAutoCommit()
  {
    return $this->childConnection->getAutoCommit();
  }

  /**
   * Private function that logs message using specified logger (if provided).
   * @param string $msg Message to log.
   */
  private function log($msg)
  {
    if (self::$logger)
    {
      // message on one line
      $msg = preg_replace("/\r?\n/", ' ', $msg);
      self::$logger->log($msg);
    }
  }

  public function __call($method, $arguments)
  {
    return $this->childConnection->$method($arguments);
  }
}
