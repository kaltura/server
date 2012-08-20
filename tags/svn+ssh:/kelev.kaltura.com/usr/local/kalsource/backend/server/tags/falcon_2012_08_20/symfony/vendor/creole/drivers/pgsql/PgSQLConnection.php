<?php
/*
 *  $Id: PgSQLConnection.php,v 1.21 2005/08/03 17:56:22 hlellelid Exp $
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
include_once 'creole/drivers/pgsql/PgSQLResultSet.php';

/**
 * PgSQL implementation of Connection.
 * 
 * @author    Hans Lellelid <hans@xmpl.org> (Creole)
 * @author    Stig Bakken <ssb@fast.no> (PEAR::DB)
 * @author    Lukas Smith (PEAR::MDB)
 * @version   $Revision: 1.21 $
 * @package   creole.drivers.pgsql
 */ 
class PgSQLConnection extends ConnectionCommon implements Connection {        
                
    /** 
     * Affected Rows of last executed query.
     * Postgres needs this for getUpdateCount()
     * We used to store the entire result set
     * instead but that can be a large dataset.
     * @var int
     */
    private $result_affected_rows;
    
    /**
     * Connect to a database and log in as the specified user.
     *
     * @param array $dsn The datasource hash.
     * @param $flags Any connection flags.
     * @access public
     * @throws SQLException
     * @return void
     */
    function connect($dsninfo, $flags = 0)
    {    
        global $php_errormsg;
                
        if (!extension_loaded('pgsql')) {
            throw new SQLException('pgsql extension not loaded');
        }

        $this->dsn = $dsninfo;
        $this->flags = $flags;
        
        $persistent = ($flags & Creole::PERSISTENT === Creole::PERSISTENT);
                
        $protocol = (isset($dsninfo['protocol'])) ? $dsninfo['protocol'] : 'tcp';
        $connstr = '';

        if ($protocol == 'tcp') {
            if (!empty($dsninfo['hostspec'])) {
                $connstr = 'host=' . $dsninfo['hostspec'];
            }
            if (!empty($dsninfo['port'])) {
                $connstr .= ' port=' . $dsninfo['port'];
            }
        }

        if (isset($dsninfo['database'])) {
            $connstr .= ' dbname=\'' . addslashes($dsninfo['database']) . '\'';
        }
        if (!empty($dsninfo['username'])) {
            $connstr .= ' user=\'' . addslashes($dsninfo['username']) . '\'';
        }
        if (!empty($dsninfo['password'])) {
            $connstr .= ' password=\'' . addslashes($dsninfo['password']) . '\'';
        }
        if (!empty($dsninfo['options'])) {
            $connstr .= ' options=' . $dsninfo['options'];
        }
        if (!empty($dsninfo['tty'])) {
            $connstr .= ' tty=' . $dsninfo['tty'];
        }
        
        if ($persistent) {
            $conn = @pg_pconnect($connstr);
        } else {
            $conn = @pg_connect($connstr);
        }
        
        if (!$conn) {
			// hide the password from connstr
			$cleanconnstr = preg_replace('/password=\'.*?\'($|\s)/', 'password=\'*********\'', $connstr);
            throw new SQLException('Could not connect', $php_errormsg, $cleanconnstr);
        }
        
        $this->dblink = $conn;        
    }
    
    /**
     * @see Connection::applyLimit()
     */
    public function applyLimit(&$sql, $offset, $limit)
    {
        if ( $limit > 0 ) {
            $sql .= " LIMIT ".$limit;
        }
        if ( $offset > 0 ) {
            $sql .= " OFFSET ".$offset;
        }
    }
    
    /**
     * @see Connection::disconnect()
     */
    function close()
    {
        $ret = @pg_close($this->dblink);
	$this->result_affected_rows = null;
        $this->dblink = null;
        return $ret;
    }
    
    /**
     * @see Connection::simpleQuery()
     */
    function executeQuery($sql, $fetchmode = null)
    {
        $result = @pg_query($this->dblink, $sql);
        if (!$result) {
            throw new SQLException('Could not execute query', pg_last_error($this->dblink), $sql);
        }
	$this->result_affected_rows = (int) @pg_affected_rows($result);

        return new PgSQLResultSet($this, $result, $fetchmode);
    }        

    /**
     * @see Connection::simpleUpdate()
     */
    function executeUpdate($sql)
    {            
        $result = @pg_query($this->dblink, $sql);
        if (!$result) {
            throw new SQLException('Could not execute update', pg_last_error($this->dblink), $sql);
        }
	$this->result_affected_rows = (int) @pg_affected_rows($result);

	return $this->result_affected_rows;
    }

    /**
     * Start a database transaction.
     * @throws SQLException
     * @return void
     */
    protected function beginTrans()
    {
        $result = @pg_query($this->dblink, "BEGIN");
        if (!$result) {
            throw new SQLException('Could not begin transaction', pg_last_error($this->dblink));
        }
    }
        
    /**
     * Commit the current transaction.
     * @throws SQLException
     * @return void
     */
    protected function commitTrans()
    {
        $result = @pg_query($this->dblink, "COMMIT");
        if (!$result) {
            throw new SQLException('Could not commit transaction', pg_last_error($this->dblink));
        }
    }

    /**
     * Roll back (undo) the current transaction.
     * @throws SQLException
     * @return void
     */
    protected function rollbackTrans()
    {
        $result = @pg_query($this->dblink, "ROLLBACK");
        if (!$result) {
            throw new SQLException('Could not rollback transaction', pg_last_error($this->dblink));
        }
    }

    /**
     * Gets the number of rows affected by the data manipulation
     * query.
     * @see Statement::getUpdateCount()
     * @return int Number of rows affected by the last query.
     */
    function getUpdateCount()
    {
	if ( $this->result_affected_rows === null ) {
		throw new SQLException('getUpdateCount called before any sql queries were executed');
	}
	return $this->result_affected_rows;
    }    

    
    /**
     * @see Connection::getDatabaseInfo()
     */
    public function getDatabaseInfo()
    {
        require_once 'creole/drivers/pgsql/metadata/PgSQLDatabaseInfo.php';
        return new PgSQLDatabaseInfo($this);
    }
    
    /**
     * @see Connection::getIdGenerator()
     */
    public function getIdGenerator()
    {
        require_once 'creole/drivers/pgsql/PgSQLIdGenerator.php';
        return new PgSQLIdGenerator($this);
    }
    
    /**
     * @see Connection::prepareStatement()
     */
    public function prepareStatement($sql) 
    {
        require_once 'creole/drivers/pgsql/PgSQLPreparedStatement.php';
        return new PgSQLPreparedStatement($this, $sql);
    }
    
    /**
     * @see Connection::prepareCall()
     */
    public function prepareCall($sql) {
        throw new SQLException('PostgreSQL does not support stored procedures.');
    }
    
    /**
     * @see Connection::createStatement()
     */
    public function createStatement()
    {
        require_once 'creole/drivers/pgsql/PgSQLStatement.php';
        return new PgSQLStatement($this);
    }
    
}
