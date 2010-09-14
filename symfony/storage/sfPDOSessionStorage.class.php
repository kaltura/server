<?php

/*
 * This file is part of the symfony package.
 * (c) 2004, 2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004, 2005 Sean Kerr.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Provides support for session storage using a PDO database abstraction layer.
 *
 * <b>Required parameters:</b>
 *
 * # <b>db_table</b> - [none] - The database table in which session data will be stored.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>database</b>     - [default]   - The database connection to use (see databases.yml).
 * # <b>db_id_col</b>    - [sess_id]   - The database column in which the session id will be stored.
 * # <b>db_data_col</b>  - [sess_data] - The database column in which the session data will be stored.
 * # <b>db_time_col</b>  - [sess_time] - The database column in which the session timestamp will be stored.
 * # <b>session_name</b> - [symfony]   - The name of the session.
 *
 * @package    symfony
 * @subpackage storage
 * @author     Mathew Toth <developer@poetryleague.com>
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfPDOSessionStorage.class.php 3329 2007-01-23 08:29:34Z fabien $
 */
class sfPDOSessionStorage extends sfSessionStorage
{
  /**
   * PDO connection
   * @var Connection
   */
  protected $db;

  /**
   * Initializes this Storage instance.
   *
   * @param sfContext A sfContext instance
   * @param array     An associative array of initialization parameters
   *
   * @return boolean true, if initialization completes successfully, otherwise false
   *
   * @throws <b>InitializationException</b> If an error occurs while initializing this Storage
   */
  public function initialize($context, $parameters = null)
  {
    // disable auto_start
    $parameters['auto_start'] = false;

    // initialize the parent
    parent::initialize($context, $parameters);

    if (!$this->getParameterHolder()->has('db_table'))
    {
      // missing required 'db_table' parameter
      $error = 'Factory configuration file is missing required "db_table" parameter for the Storage category';

      throw new sfInitializationException($error);
    }

    // use this object as the session handler
    session_set_save_handler(array($this, 'sessionOpen'),
                             array($this, 'sessionClose'),
                             array($this, 'sessionRead'),
                             array($this, 'sessionWrite'),
                             array($this, 'sessionDestroy'),
                             array($this, 'sessionGC'));

    // start our session
    session_start();
  }

  /**
  * Closes a session.
  *
  * @return boolean true, if the session was closed, otherwise false
  */
  public function sessionClose()
  {
    // do nothing
    return true;
  }

  /**
   * Destroys a session.
   *
   * @param string A session ID
   *
   * @return boolean true, if the session was destroyed, otherwise an exception is thrown
   *
   * @throws <b>DatabaseException</b> If the session cannot be destroyed
   */
  public function sessionDestroy($id)
  {
    // get table/column
    $db_table  = $this->getParameterHolder()->get('db_table');
    $db_id_col = $this->getParameterHolder()->get('db_id_col', 'sess_id');

    // delete the record associated with this id
    $sql = 'DELETE FROM '.$db_table.' WHERE '.$db_id_col.'= ?';

    try
    {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(1, $id, PDO::PARAM_STR); // setString(1, $id);
      $stmt->execute();
    }
    catch (PDOException $e)
    {
      $error = sprintf('PDOException was thrown when trying to manipulate session data. Message: %s', $e->getMessage());

      throw new sfDatabaseException($error);
    }
  }

  /**
   * Cleans up old sessions.
   *
   * @param int The lifetime of a session
   *
   * @return boolean true, if old sessions have been cleaned, otherwise an exception is thrown
   *
   * @throws <b>DatabaseException</b> If any old sessions cannot be cleaned
   */
  public function sessionGC($lifetime)
  {
    // determine deletable session time
    $time = time() - $lifetime;

    // get table/column
    $db_table    = $this->getParameterHolder()->get('db_table');
    $db_time_col = $this->getParameterHolder()->get('db_time_col', 'sess_time');

    // delete the record associated with this id
    $sql = 'DELETE FROM '.$db_table.' WHERE '.$db_time_col.' < '.$time;

    try
    {
      $this->db->query($sql);
      return true;
    }
    catch (PDOException $e)
    {
      $error = sprintf('PDOException was thrown when trying to manipulate session data. Message: %s', $e->getMessage());

      throw new sfDatabaseException($error);
    }
  }

  /**
   * Opens a session.
   *
   * @param string
   * @param string
   *
   * @return boolean true, if the session was opened, otherwise an exception is thrown
   *
   * @throws <b>DatabaseException</b> If a connection with the database does not exist or cannot be created
   */
  public function sessionOpen($path, $name)
  {
    // what database are we using?
    $database = $this->getParameterHolder()->get('database', 'default');

    $this->db = $this->getContext()->getDatabaseConnection($database);
    if ($this->db == null || !$this->db instanceof PDO)
    {
      $error = 'PDO dabatase connection doesn\'t exist. Unable to open session.';

      throw new sfDatabaseException($error);
    }

    return true;
  }

  /**
   * Reads a session.
   *
   * @param string A session ID
   *
   * @return boolean true, if the session was read, otherwise an exception is thrown
   *
   * @throws <b>DatabaseException</b> If the session cannot be read
   */
  public function sessionRead($id)
  {
    // get table/columns
    $db_table    = $this->getParameterHolder()->get('db_table');
    $db_data_col = $this->getParameterHolder()->get('db_data_col', 'sess_data');
    $db_id_col   = $this->getParameterHolder()->get('db_id_col', 'sess_id');
    $db_time_col = $this->getParameterHolder()->get('db_time_col', 'sess_time');

    try
    {
      $sql = 'SELECT '.$db_data_col.' FROM '.$db_table.' WHERE '.$db_id_col.'=?';

      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(1, $id, PDO::PARAM_STR, 255);

      $stmt->execute();
      if ($data = $stmt->fetchColumn())
      {
        return $data;
      }
      else
      {
        // session does not exist, create it
        $sql = 'INSERT INTO '.$db_table.'('.$db_id_col.', '.$db_data_col.', '.$db_time_col.') VALUES (?, ?, ?)';

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_STR); // setString(1, $id);
        $stmt->bindValue(2, '', PDO::PARAM_STR); // setString(2, '');
        $stmt->bindValue(3, time(), PDO::PARAM_INT); // setInt(3, time());
        $stmt->execute();

        return '';
      }
    }
    catch (PDOException $e)
    {
      $error = sprintf('PDOException was thrown when trying to manipulate session data. Message: %s', $e->getMessage());

      throw new sfDatabaseException($error);
    }
  }

  /**
   * Writes session data.
   *
   * @param string A session ID
   * @param string A serialized chunk of session data
   *
   * @return boolean true, if the session was written, otherwise an exception is thrown
   *
   * @throws <b>DatabaseException</b> If the session data cannot be written
   */
  public function sessionWrite($id, $data)
  {
    // get table/column
    $db_table    = $this->getParameterHolder()->get('db_table');
    $db_data_col = $this->getParameterHolder()->get('db_data_col', 'sess_data');
    $db_id_col   = $this->getParameterHolder()->get('db_id_col', 'sess_id');
    $db_time_col = $this->getParameterHolder()->get('db_time_col', 'sess_time');

    $sql = 'UPDATE '.$db_table.' SET '.$db_data_col.' = ?, '.$db_time_col.' = '.time().' WHERE '.$db_id_col.'= ?';

    try
    {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(1, $data, PDO::PARAM_STR); // setString(1, $data);
      $stmt->bindParam(2, $id, PDO::PARAM_STR); // setString(2, $id);
      $stmt->execute();
      return true;
    }

    catch (PDOException $e)
    {
      $error = sprintf('PDOException was thrown when trying to manipulate session data. Message: %s', $e->getMessage());

      throw new sfDatabaseException($error);
    }

    return false;
  }

  /**
   * Executes the shutdown procedure.
   *
   */
  public function shutdown()
  {
  }
}
