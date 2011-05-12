<?php

/*
 * This file is part of the symfony package.
 * (c) 2004, 2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004, 2005 Sean Kerr.
 *
 * The original version the file is based on is licensed under the LGPL, but a special license was granted.
 * Please see the licenses/LICENSE.Agavi file
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Provides support for session storage using a CreoleDb database abstraction layer.
 *
 * <b>Required parameters:</b>
 *
 * # <b>db_table</b> - [none] - The database table in which session data will be
 *                              stored.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>database</b>     - [default]   - The database connection to use
 *                                       (see databases.ini).
 * # <b>db_id_col</b>    - [sess_id]   - The database column in which the
 *                                       session id will be stored.
 * # <b>db_data_col</b>  - [sess_data] - The database column in which the
 *                                       session data will be stored.
 * # <b>db_time_col</b>  - [sess_time] - The database column in which the
 *                                       session timestamp will be stored.
 * # <b>session_name</b> - [Agavi]    - The name of the session.
 *
 * @package    symfony
 * @subpackage storage
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @author     Veikko Mäkinen <mail@veikkomakinen.com>
 * @version    SVN: $Id: sfCreoleSessionStorage.class.php 2995 2006-12-09 18:01:32Z fabien $
 */
class sfCreoleSessionStorage extends sfSessionStorage
{
  /**
   * Creole Database Connection
   * @var Connection
   */
  protected $db;

  /**
   * Initialize this Storage.
   *
   * @param Context A Context instance.
   * @param array   An associative array of initialization parameters.
   *
   * @return bool true, if initialization completes successfully, otherwise
   *              false.
   *
   * @throws <b>InitializationException</b> If an error occurs while
   *                                        initializing this Storage.
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
  * Close a session.
  *
  * @return bool true, if the session was closed, otherwise false.
  */
  public function sessionClose()
  {
    // do nothing
    return true;
  }

  /**
   * Destroy a session.
   *
   * @param string A session ID.
   *
   * @return bool true, if the session was destroyed, otherwise an exception
   *              is thrown.
   *
   * @throws <b>DatabaseException</b> If the session cannot be destroyed.
   */
  public function sessionDestroy($id)
  {
    // get table/column
    $db_table  = $this->getParameterHolder()->get('db_table');
    $db_id_col = $this->getParameterHolder()->get('db_id_col', 'sess_id');

    // delete the record associated with this id
    $sql = 'DELETE FROM ' . $db_table . ' WHERE ' . $db_id_col . '=?';

    try
    {
      $stmt = $this->db->prepareStatement($sql);
      $stmt->setString(1, $id);
      $stmt->executeUpdate();
    }
    catch (SQLException $e) {
      $error = 'Creole SQLException was thrown when trying to manipulate session data. ';
      $error .= 'Message: ' . $e->getMessage();
      throw new sfDatabaseException($error);
    }
  }

  /**
   * Cleanup old sessions.
   *
   * @param int The lifetime of a session.
   *
   * @return bool true, if old sessions have been cleaned, otherwise an
   *              exception is thrown.
   *
   * @throws <b>DatabaseException</b> If any old sessions cannot be cleaned.
   */
  public function sessionGC($lifetime)
  {
    // determine deletable session time
    $time = time() - $lifetime;

    // get table/column
    $db_table    = $this->getParameterHolder()->get('db_table');
    $db_time_col = $this->getParameterHolder()->get('db_time_col', 'sess_time');

    // delete the record associated with this id
    $sql = 'DELETE FROM ' . $db_table . ' ' .
      'WHERE ' . $db_time_col . ' < ' . $time;

    try
    {
      $this->db->executeQuery($sql);
      return true;
    }
    catch (SQLException $e)
    {
      $error = 'Creole SQLException was thrown when trying to manipulate session data. ';
      $error .= 'Message: ' . $e->getMessage();
      throw new sfDatabaseException($error);
    }
  }

  /**
   * Open a session.
   *
   * @param string
   * @param string
   *
   * @return bool true, if the session was opened, otherwise an exception is
   *              thrown.
   *
   * @throws <b>DatabaseException</b> If a connection with the database does
   *                                  not exist or cannot be created.
   */
  public function sessionOpen($path, $name)
  {
    // what database are we using?
    $database = $this->getParameterHolder()->get('database', 'default');

    // autoload propel propely if we're reusing the propel connection for session storage
    if ($this->getContext()->getDatabaseManager()->getDatabase($database) instanceof sfPropelDatabase && !Propel::isInit())
    {
      $error = 'Creole dabatase connection is the same as the propel database connection, but could not be initialized.';
      throw new sfDatabaseException($error);
    }

    $this->db = $this->getContext()->getDatabaseConnection($database);
    if ($this->db == null || !$this->db instanceof Connection)
    {
      $error = 'Creole dabatase connection doesn\'t exist. Unable to open session.';
      throw new sfDatabaseException($error);
    }

    return true;
  }

  /**
   * Read a session.
   *
   * @param string A session ID.
   *
   * @return bool true, if the session was read, otherwise an exception is
   *              thrown.
   *
   * @throws <b>DatabaseException</b> If the session cannot be read.
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
      $sql = 'SELECT ' . $db_data_col . ' FROM ' . $db_table . ' WHERE ' . $db_id_col . '=?';

      $stmt = $this->db->prepareStatement($sql);
      $stmt->setString(1, $id);

      $dbRes = $stmt->executeQuery(ResultSet::FETCHMODE_NUM);

      if ($dbRes->next())
      {
        $data = $dbRes->getString(1);
        return $data;
      }
      else
      {
        // session does not exist, create it
        $sql = 'INSERT INTO ' . $db_table . '('.$db_id_col.','.$db_data_col.','.$db_time_col;
        $sql .= ') VALUES (?,?,?)';

        $stmt = $this->db->prepareStatement($sql);
        $stmt->setString(1, $id);
        $stmt->setString(2, '');
        $stmt->setInt(3, time());
        $stmt->executeUpdate();
        return '';
      }
    }
    catch (SQLException $e)
    {
      $error = 'Creole SQLException was thrown when trying to manipulate session data. ';
      $error .= 'Message: ' . $e->getMessage();
      throw new sfDatabaseException($error);
    }
  }

  /**
   * Write session data.
   *
   * @param string A session ID.
   * @param string A serialized chunk of session data.
   *
   * @return bool true, if the session was written, otherwise an exception is
   *              thrown.
   *
   * @throws <b>DatabaseException</b> If the session data cannot be written.
   */
  public function sessionWrite($id, $data)
  {
    // get table/column
    $db_table    = $this->getParameterHolder()->get('db_table');
    $db_data_col = $this->getParameterHolder()->get('db_data_col', 'sess_data');
    $db_id_col   = $this->getParameterHolder()->get('db_id_col', 'sess_id');
    $db_time_col = $this->getParameterHolder()->get('db_time_col', 'sess_time');

    $sql = 'UPDATE ' . $db_table . ' SET ' . $db_data_col . '=?, ' . $db_time_col . ' = ' . time() .
      ' WHERE ' . $db_id_col . '=?';

    try
    {
      $stmt = $this->db->prepareStatement($sql);
      $stmt->setString(1, $data);
      $stmt->setString(2, $id);
      $stmt->executeUpdate();
      return true;
    }

    catch (SQLException $e)
    {
      $error = 'Creole SQLException was thrown when trying to manipulate session data. ';
      $error .= 'Message: ' . $e->getMessage();
      throw new sfDatabaseException($error);
    }

    return false;
  }

  /**
   * Execute the shutdown procedure.
   *
   * @return void
   */
  public function shutdown()
  {
  }

}
