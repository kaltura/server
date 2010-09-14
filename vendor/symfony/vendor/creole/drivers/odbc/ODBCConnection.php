<?php
/*
 *  $Id: ODBCConnection.php,v 1.6 2006/01/17 19:44:39 hlellelid Exp $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://creole.phpdb.org>.
 */

require_once 'creole/Connection.php';
require_once 'creole/common/ConnectionCommon.php';
require_once 'creole/drivers/odbc/adapters/ODBCAdapter.php';

/**
 * ODBC implementation of Connection.
 *
 * @author    Dave Lawson <dlawson@masterytech.com>
 * @version   $Revision: 1.6 $
 * @package   creole.drivers.odbc
 */
class ODBCConnection extends ConnectionCommon implements Connection {

    /**
     * Implements driver-specific behavior
     * @var ODBCAdapter
     */
    protected $adapter = null;

    /**
     * Last ODBC result resource from executeQuery/executeUpdate. Used in getUpdateCount()
     * @var ODBCResultResource
     */
    protected $odbcresult = null;

    /**
     * @see Connection::connect()
     */
    public function connect($dsninfo, $flags = 0)
    {
		if (!function_exists('odbc_connect'))
            throw new SQLException('odbc extension not loaded');

        $adapterclass = isset($dsninfo['adapter']) ? $dsninfo['adapter'] : null;

        if (!$adapterclass)
            $adapterclass = 'ODBCAdapter';
        else
            $adapterclass .= 'Adapter';

        Creole::import('creole.drivers.odbc.adapters.' . $adapterclass);
        $this->adapter = new $adapterclass();

        $this->dsn = $dsninfo;
        $this->flags = $flags;

        if ( !($this->flags & Creole::COMPAT_ASSOC_LOWER) && !$this->adapter->preservesColumnCase())
        {
            trigger_error('Connection created without Creole::COMPAT_ASSOC_LOWER, ' .
                          'but driver does not support case preservation.',
                          E_USER_WARNING);
            $this->flags != Creole::COMPAT_ASSOC_LOWER;
        }

        $persistent = ($flags & Creole::PERSISTENT) === Creole::PERSISTENT;

        if ($dsninfo['database'])
            $odbcdsn = $dsninfo['database'];
        elseif ($dsninfo['hostspec'])
            $odbcdsn = $dsninfo['hostspec'];
        else
            $odbcdsn = 'localhost';

        $user = @$dsninfo['username'];
        $pw = @$dsninfo['password'];

        $connect_function = $persistent ? 'odbc_pconnect' : 'odbc_connect';

        $conn = @$connect_function($odbcdsn, $user, $pw, SQL_CUR_USE_IF_NEEDED);

        if (!is_resource($conn))
            throw new SQLException('connect failed', $this->nativeError(), $odbcdsn);

        $this->dblink = $conn;

        /**
         * This prevents blob fields from being fetched when a row is loaded
         * from a recordset. Clob fields however are loaded with up to
         * 'odbc.defaultlrl' data. This should be the default anyway, but we'll
         * set it here just to keep things consistent.
         */
        @odbc_binmode(0, ODBC_BINMODE_PASSTHRU);
        @odbc_longreadlen(0, ini_get('odbc.defaultlrl'));
    }

    /**
     * @see Connection::close()
     */
    public function close()
    {
        $ret = true;

        $this->adapter = null;
        $this->odbcresult = null;

        if ($this->dblink !== null)
        {
            $ret = @odbc_close($this->dblink);
            $this->dblink = null;
        }

        return $ret;
    }

    /**
     * Shouldn't this be in ConnectionCommon.php?
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Returns a formatted ODBC error string.
     * @return string
     */
    public function nativeError()
    {
        if ($this->dblink && is_resource($this->dblink))
            $errstr = '[' . @odbc_error($this->dblink) . '] ' . @odbc_errormsg($this->dblink);
        else
            $errstr = '[' . @odbc_error() . '] ' . @odbc_errormsg();

        return $errstr;
    }

    /**
     * Returns driver-specific ODBCAdapter.
     * @return ODBCAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @see Connection::getDatabaseInfo()
     */
    public function getDatabaseInfo()
    {
        require_once 'creole/drivers/odbc/metadata/ODBCDatabaseInfo.php';
        return new ODBCDatabaseInfo($this);
    }

    /**
     * @see Connection::getIdGenerator()
     */
    public function getIdGenerator()
    {
        return $this->adapter->getIdGenerator($this);
    }

    /**
     * Creates the appropriate ResultSet
     * @return ResultSet
     */
    public function createResultSet($odbcresult, $fetchmode)
    {
        return $this->adapter->createResultSet($this, $odbcresult, $fetchmode);
    }

    /**
     * @see Connection::prepareStatement()
     */
    public function prepareStatement($sql)
    {
        require_once 'creole/drivers/odbc/ODBCPreparedStatement.php';
        return new ODBCPreparedStatement($this, $sql);
    }

    /**
     * @see Connection::createStatement()
     */
    public function createStatement()
    {
        require_once 'creole/drivers/odbc/ODBCStatement.php';
        return new ODBCStatement($this);
    }

    /**
     * @todo To be implemented
     * @see Connection::prepareCall()
     */
    public function prepareCall($sql)
    {
        throw new SQLException('Stored procedures not currently implemented.');
    }

    /**
     * @see Connection::applyLimit()
     */
    public function applyLimit(&$sql, $offset, $limit)
    {
        if ($this->adapter->hasLimitOffset())
            $this->adapter->applyLimit($sql, $offset, $limit);
    }

    /**
     * @see Connection::executeQuery()
     */
    public function executeQuery($sql, $fetchmode = null)
    {
        if ($this->odbcresult)
            $this->odbcresult = null;

        $r = @odbc_exec($this->dblink, $sql);

        if ($r === false)
            throw new SQLException('Could not execute query', $this->nativeError(), $sql);

        $this->odbcresult = new ODBCResultResource($r);

        return $this->createResultSet($this->odbcresult, $fetchmode);
    }

    /**
     * @see Connection::executeUpdate()
     */
    public function executeUpdate($sql)
    {
        if ($this->odbcresult)
            $this->odbcresult = null;

        $r = @odbc_exec($this->dblink, $sql);

        if ($r === false)
            throw new SQLException('Could not execute update', $this->nativeError(), $sql);

        $this->odbcresult = new ODBCResultResource($r);

        return $this->getUpdateCount();
    }

    /**
     * Start a database transaction.
     * @throws SQLException
     * @return void
     */
    protected function beginTrans()
    {
        if ($this->adapter->supportsTransactions()) {
            @odbc_autocommit($this->dblink, false);
            if (odbc_error($this->dblink) == 'S1C00') {
                throw new SQLException('Could not begin transaction', $this->nativeError());
            }
        }
    }
    
    /**
     * Commit the current transaction.
     * @throws SQLException
     * @return void
     */
    protected function commitTrans()
    {
        if ($this->adapter->supportsTransactions()) {
            $result = @odbc_commit($this->dblink);
            if (!$result) {
                throw new SQLException('Could not commit transaction', $this->nativeError());
            }
            @odbc_autocommit($this->dblink, true);
            if (odbc_error($this->dblink) == 'S1C00') {
                throw new SQLException('Could not commit transaction (autocommit failed)', $this->nativeError());
            }
        }
    }

    /**
     * Roll back (undo) the current transaction.
     * @throws SQLException
     * @return void
     */
    protected function rollbackTrans()
    {
        if ($this->adapter->supportsTransactions()) {
            $result = @odbc_rollback($this->dblink);
            if (!$result) {
                throw new SQLException('Could not rollback transaction', $this->nativeError());
            }
            @odbc_autocommit($this->dblink, true);
            if (odbc_error($this->dblink) == 'S1C00') {
                throw new SQLException('Could not rollback transaction (autocommit failed)', $this->nativeError());
            }
        }
    }

    /**
     * @see Connection::getUpdateCount()
     */
    public function getUpdateCount()
    {
        if ($this->odbcresult === null)
            return 0;

        $n = @odbc_num_rows($this->odbcresult->getHandle());

        if ($n == -1)
            throw new SQLException('Could not retrieve update count', $this->nativeError());

        return (int) $n;
    }

}

/**
 * This is a simple wrapper class to manage the lifetime of an ODBC result resource
 * (returned by odbc_exec(), odbc_execute(), etc.) We use a separate class because
 * the resource can be shared by both ODBCConnection and an ODBCResultSet at the
 * same time. ODBCConnection hangs on to the last result resource to be used in
 * its getUpdateCount() method. It also passes this resource to new instances of
 * ODBCResultSet. At some point the resource has to be cleaned up via
 * odbc_free_result(). Using this class as a wrapper, we can pass around multiple
 * references to the same resource. PHP's reference counting mechanism will clean
 * up the resource when its no longer used via ODBCResultResource::__destruct().
 * @package   creole.drivers.odbc
 */
class ODBCResultResource
{
    /**
     * @var resource ODBC result resource returned by {@link odbc_exec()}/{@link odbc_execute()}.
     */
    protected $handle = null;

    public function __construct($handle)
    {
        if (is_resource($handle))
            $this->handle = $handle;
    }

    public function __destruct()
    {
        if ($this->handle !== null)
            @odbc_free_result($this->handle);
    }

    public function getHandle()
    {
        return $this->handle;
    }

}