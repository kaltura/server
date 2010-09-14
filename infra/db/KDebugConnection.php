<?php
class KDebugConnection implements Connection
{
  /** @var Connection */
  private $childConnection = null;

  /** @var int */
  private $numQueriesExecuted = 0;

  /** @var string */
  private $lastExecutedQuery = '';

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
    return $this->childConnection->applyLimit($sql, $offset, $limit);
  }

  /**
   * @see Connection::close()
   */
  public function close()
  {
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
    $start = microtime(true);

    $retval = $this->childConnection->executeQuery($sql, $fetchmode);

    $end = microtime(true);
    $elapsedTime = $end - $start;
    
    KalturaLog::debug(sprintf("{sfCreole} executeQuery(): [%.2f ms] %s", $elapsedTime, $sql));

    return $retval;
  }

  /**
  * @see Connection::executeUpdate()
  **/
  public function executeUpdate($sql)
  {
    KalturaLog::debug("{sfCreole} executeUpdate(): $sql");
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
    return $this->childConnection->begin();
  }

  /**
   * @see Connection::commit()
   */
  public function commit()
  {
    return $this->childConnection->commit();
  }

  /**
   * @see Connection::rollback()
   */
  public function rollback()
  {
    return $this->childConnection->rollback();
  }

  /**
   * @see Connection::setAutoCommit()
   */
  public function setAutoCommit($bit)
  {
    return $this->childConnection->setAutoCommit($bit);
  }

  /**
   * @see Connection::getAutoCommit()
   */
  public function getAutoCommit()
  {
    return $this->childConnection->getAutoCommit();
  }

  public function __call($method, $arguments)
  {
    return $this->childConnection->$method($arguments);
  }
}