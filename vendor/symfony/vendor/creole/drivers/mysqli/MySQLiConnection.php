<?php
/*
 * $Id: MySQLiConnection.php,v 1.7 2004/09/18 09:29:22 sb Exp $
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
include_once 'creole/drivers/mysqli/MySQLiResultSet.php';

/**
 * MySQLi implementation of Connection.
 *
 *
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @version   $Revision: 1.7 $
 * @package   creole.drivers.mysqli
 */
class MySQLiConnection extends ConnectionCommon implements Connection {
    /** Current database (used in mysqli_select_db()). */
    private $database;

    /**
     * Connect to a database and log in as the specified user.
     *
     * @param $dsn the data source name (see DB::parseDSN for syntax)
     * @param $flags Any conneciton flags.
     * @access public
     * @throws SQLException
     * @return void
     */
    public function connect($dsninfo, $flags = 0)
    {
        if (!extension_loaded('mysqli')) {
            throw new SQLException('mysqli extension not loaded');
        }

        $this->dsn = $dsninfo;
        $this->flags = $flags;
		
		$dbhost = null;
		

        if (isset($dsninfo['protocol']) && $dsninfo['protocol'] == 'unix') {
            $dbhost = ':' . $dsninfo['socket'];
        } else {
            $dbhost = $dsninfo['hostspec'] ? $dsninfo['hostspec'] : 'localhost';

            if (!empty($dsninfo['port'])) {
                $dbhost .= ':' . $dsninfo['port'];
            }
        }

		$host = !empty($dsninfo['hostspec']) ? $dsninfo['hostspec'] : null;
        $user = !empty($dsninfo['username']) ? $dsninfo['username'] : null;
        $pw = !empty($dsninfo['password']) ? $dsninfo['password'] : null;
		$port = !empty($dsninfo['port']) ? $dsninfo['port'] : null;
		$socket = !empty($dsninfo['socket']) ? $dsninfo['socket'] : null;
		$database = !empty($dsninfo['database']) ? $dsninfo['database'] : null;

		$encoding = !empty($dsninfo['encoding']) ? $dsninfo['encoding'] : null;

        @ini_set('track_errors', true);

		$conn = mysqli_connect($host, $user, $pw, $database, $port, $socket);

            @ini_restore('track_errors');

        if (empty($conn)) {
            if (($err = @mysqli_error()) != '') {
                throw new SQLException("connect failed", $err);
            } elseif (empty($php_errormsg)) {
                throw new SQLException("connect failed");
            } else {
                throw new SQLException("connect failed", $php_errormsg);
            }
        }

        if ($dsninfo['database']) {
            if (!@mysqli_select_db($conn, $dsninfo['database'])) {
               switch(mysqli_errno($conn)) {
                        case 1049:
                            $exc = new SQLException("no such database", mysqli_error($conn));
                        break;
                        case 1044:
                            $exc = new SQLException("access violation", mysqli_error($conn));
                        break;
                        default:
                           $exc = new SQLException("cannot select database", mysqli_error($conn));
                }

                throw $exc;

            }

            // fix to allow calls to different databases in the same script
            $this->database = $dsninfo['database'];
        }

        $this->dblink = $conn;

        if ($encoding) {
			$this->executeUpdate("SET NAMES " . $encoding);
		}
    }

    /**
     * @see Connection::getDatabaseInfo()
     */
    public function getDatabaseInfo()
    {
        require_once 'creole/drivers/mysqli/metadata/MySQLiDatabaseInfo.php';
        return new MySQLiDatabaseInfo($this);
    }

    /**
     * @see Connection::getIdGenerator()
     */
    public function getIdGenerator()
    {
        require_once 'creole/drivers/mysqli/MySQLiIdGenerator.php';
        return new MySQLiIdGenerator($this);
    }

    /**
     * @see Connection::prepareStatement()
     */
    public function prepareStatement($sql)
    {
        require_once 'creole/drivers/mysqli/MySQLiPreparedStatement.php';
        return new MySQLiPreparedStatement($this, $sql);
    }

    /**
     * @see Connection::prepareCall()
     */
    public function prepareCall($sql) {
        throw new SQLException('MySQL does not support stored procedures.');
    }

    /**
     * @see Connection::createStatement()
     */
    public function createStatement()
    {
        require_once 'creole/drivers/mysqli/MySQLiStatement.php';
        return new MySQLiStatement($this);
    }

    /**
     * @see Connection::disconnect()
     */
    public function close()
    {
        $ret = mysqli_close($this->dblink);
        $this->dblink = null;
        return $ret;
    }

    /**
     * @see Connection::applyLimit()
     */
    public function applyLimit(&$sql, $offset, $limit)
    {
        if ( $limit > 0 ) {
            $sql .= " LIMIT " . ($offset > 0 ? $offset . ", " : "") . $limit;
        } else if ( $offset > 0 ) {
            $sql .= " LIMIT " . $offset . ", 18446744073709551615";
        }
    }

    /**
     * @see Connection::executeQuery()
     */
    public function executeQuery($sql, $fetchmode = null)
    {
        $this->lastQuery = $sql;

        if ($this->database) {
            if (!@mysqli_select_db($this->dblink, $this->database)) {
                throw new SQLException('No database selected', mysqli_error($this->dblink));
            }
        }

        $result = @mysqli_query($this->dblink, $sql);

        if (!$result) {
            throw new SQLException('Could not execute query', mysqli_error($this->dblink), $sql);
        }

        return new MySQLiResultSet($this, $result, $fetchmode);
    }

    /**
     * @see Connection::executeUpdate()
     */
    public function executeUpdate($sql)
    {
        $this->lastQuery = $sql;

        if ($this->database) {
            if (!@mysqli_select_db($this->dblink, $this->database)) {
                    throw new SQLException('No database selected', mysqli_error($this->dblink));
            }
        }

        $result = @mysqli_query($this->dblink, $sql);

        if (!$result) {
            throw new SQLException('Could not execute update', mysqli_error($this->dblink), $sql);
        }

        return (int) mysqli_affected_rows($this->dblink);
    }

    /**
     * Start a database transaction.
     * @throws SQLException
     * @return void
     */
    protected function beginTrans()
    {
        if (!mysqli_autocommit($this->dblink, FALSE)) {
            throw new SQLException('Could not begin transaction', mysqli_error($this->dblink));
        }
    }

    /**
     * Commit the current transaction.
     * @throws SQLException
     * @return void
     */
    protected function commitTrans()
    {
        if ($this->database) {
            if (!@mysqli_select_db($this->dblink, $this->database)) {
                 throw new SQLException('No database selected', mysqli_error($this->dblink));
            }
        }

        if (!mysqli_commit($this->dblink)) {
            throw new SQLException('Can not commit transaction', mysqli_error($this->dblink));                
        }

        mysqli_autocommit($this->dblink, TRUE);
    }

    /**
     * Roll back (undo) the current transaction.
     * @throws SQLException
     * @return void
     */
    protected function rollbackTrans()
    {
        if ($this->database) {
            if (!@mysqli_select_db($this->dblink, $this->database)) {
                throw new SQLException('No database selected', mysqli_error($this->dblink));
            }
        }

        if (!mysqli_rollback($this->dblink)) {
            throw new SQLException('Could not rollback transaction', mysqli_error($this->dblink));
        }

        mysqli_autocommit($this->dblink, TRUE);
    }

    /**
     * Gets the number of rows affected by the data manipulation
     * query.
     *
     * @return int Number of rows affected by the last query.
     */
    public function getUpdateCount()
    {
        return (int) @mysqli_affected_rows($this->dblink);
    }
}
