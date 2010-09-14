<?php
/*
 *  $Id: Connection.php,v 1.29 2005/10/17 19:03:50 dlawson_mi Exp $
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

include_once 'creole/ResultSet.php'; // we need this for the fetchmode ResultSet flags (constants) that are passed to executeQuery()

/**
 * Connection is an abstract base class for DB dialect implementations, and must be
 * inherited by all such.
 * 
 * Developer notes:
 *  (1) Make sure that your Connection class can be serialized.  See the ConnectionCommon __sleep() and __wakeup() implimentation.
 * 
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.29 $
 * @package   creole
 */
interface Connection {

    // Constants that define transaction isolation levels.
    // [We don't have any code using these yet, so there's no need
    // to initialize these values at this point.]
    // const TRANSACTION_NONE = 0;
    // const TRANSACTION_READ_UNCOMMITTED = 1;
    // const TRANSACTION_READ_COMMITTED = 2;
    // const TRANSACTION_REPEATABLE_READ = 3;
    // const TRANSACTION_SERIALIZABLE = 4;
    
    /**
     * Connect to a database and log in as the specified user.
     *
     * @param array $dsn The PEAR-style data source hash.
     * @param int $flags (optional) Flags for connection (e.g. Creole::PERSISTENT).  These flags
     *                   may apply to any of the driver classes.
     */
    public function connect($dsn, $flags = false);    
    
    /**
     * Get the PHP native resource for the database connection/link.
     * @return resource
     */
    public function getResource();
        
    /**
     * Get any flags that were passed to connection.
     * @return int
     */
    public function getFlags();        
    
    /**
     * Get the DSN array used by connect() method to connect to database.
     * @see connect()
     * @return array
     */
    public function getDSN();      
    
    /**
     * Gets a DatabaseInfo class for the current database.
     *
     * This is not modeled on the JDBC MetaData class, but provides a possibly more 
     * useful metadata system.  All the same, there may eventually be a getMetaData()
     * which returns a class that behaves like JDBC's DatabaseMetaData.
     *
     * @return DatabaseInfo
     */
    public function getDatabaseInfo();
    
    /**
     * Loads and returns an IdGenerator object for current RDBMS.
     * @return IdGenerator
     */
    public function getIdGenerator();
    
    /**
     * Prepares a query for multiple execution with execute().
     *
     * With some database backends, this is emulated.
     * prepare() requires a generic query as string like
     * "INSERT INTO numbers VALUES(?,?,?)". The ? are placeholders.
        * 
     * IMPORTANT:  All occurrences of the placeholder (?) will be assumed
     * to be a parameter.  Therefore be sure not to have ? anywhere else in
     * the query.
     * 
     * So, ... DO NOT MIX WILDCARDS WITH ALREADY-PREPARED QUERIES
     *
     * INCORRECT:
     *     SELECT * FROM mytable WHERE id = ? AND title = 'Where are you?' and body LIKE ?
     * 
     * CORRECT:
     *    SELECT * FROM mytable WHERE id = ? AND title = ? and body LIKE ?
     * 
     * @param string $sql The query to prepare.
     * @return PreparedStatement
     * @throws SQLException
     * @see PreparedStatement::execute()
     */
    public function prepareStatement($sql);
    
    /**
     * Creates a new empty Statement.
     * @return Statement
     */
    public function createStatement();
    
    /**
     * If RDBMS supports native LIMIT/OFFSET then query SQL is modified
     * so that no emulation is performed in ResultSet.
     * 
     * @param string &$sql The query that will be modified.
     * @param int $offset
     * @param int $limit
     * @return void
     * @throws SQLException - if unable to modify query for any reason.
     */
    public function applyLimit(&$sql, $offset, $limit);
    
    /**
     * Executes the SQL query in this PreparedStatement object and returns the resultset.
     * 
     * @param string $sql The SQL statement.
     * @param int $fetchmode
     * @return object ResultSet
     * @throws SQLException if a database access error occurs.
     */
    public function executeQuery($sql, $fetchmode = null);

    /**
     * Executes the SQL INSERT, UPDATE, or DELETE statement.
     * 
     * @param string $sql This method may optionally be called with the SQL statement.
     * @return int Number of affected rows (or 0 for drivers that return nothing).
     * @throws SQLException if a database access error occurs.
     */
    public function executeUpdate($sql);
    
    /**
     * Creates a CallableStatement object for calling database stored procedures.
     * 
     * @param string $sql
     * @return CallableStatement
     */
    public function prepareCall($sql);
    
    /**
     * Free the db resources.
     * @return void
     */
    public function close();
    
    /**
     * Returns false if connection is closed.
     * @return boolean
     */
    public function isConnected();
    
    /**
     * Get auto-commit status.
     *
     * @return boolean
     */
    public function getAutoCommit();
    
    /**
     * Enable/disable automatic commits.
     * 
     * Pushes SQLWarning onto $warnings stack if the autocommit value is being changed mid-transaction. This function
     * is overridden by driver classes so that they can perform the necessary begin/end transaction SQL.
     * 
     * If auto-commit is being set to TRUE, then the current transaction will be committed immediately.
     * 
     * @param boolean $bit New value for auto commit.
     * @return void
     */
    public function setAutoCommit($bit);
    
    /**
     * Begins a transaction (if supported).
     *
     */
    public function begin();
    
    /**
     * Commits statements in a transaction.
     *
     */
    public function commit();
    
    /**
     * Rollback changes in a transaction.
     *
     */
    public function rollback();
    
    /**
     * Gets the number of rows affected by the data manipulation
     * query.
     *
     * @return int Number of rows affected by the last query.
     */
    public function getUpdateCount();

}