<?php
/*
 *  $Id: PreparedStatement.php,v 1.21 2005/03/29 16:56:09 gamr Exp $
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

/**
 * Interface for a pre-compiled SQL statement.
 * 
 * Many drivers do not take advantage of pre-compiling SQL statements; for these
 * cases the precompilation is emulated.  This emulation comes with slight penalty involved
 * in parsing the queries, but provides other benefits such as a cleaner object model and ability
 * to work with BLOB and CLOB values w/o needing special LOB-specific routines.
 * 
 * This class is abstract because there are driver-specific implementations in [clearly] how queries
 * are executed, and how parameters are bound.
 * 
 * This class is not as abstract as the JDBC version.  For exmple, if you are using a driver
 * that uses name-based query param substitution, then you'd better bind your variables to
 * names rather than index numbers.  e.g. in Oracle
 * <code>
 *            $stmt = $conn->prepareStatement("INSERT INTO users (name, passwd) VALUES (:name, :pass)");
 *            $stmt->setString(":name", $name);
 *            $stmt->executeUpdate();            
 * </code>
 * 
 * Developer note:  In many ways this interface is an extension of the Statement interface.  However, due 
 * to limitations in PHP5's interface extension model (specifically that you cannot change signatures on
 * methods defined in parent interface), we cannot extend the Statement interface.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.21 $
 * @package   creole
 */
interface PreparedStatement {
     
    /**
     * Gets the db Connection that created this statement.
     * @return Connection
     */
    public function getConnection();    

    /**
     * Get the PHP native resource for the statement (if supported).
     * @return resource
     */
    public function getResource();
        
    /**
     * Free resources associated with this statement.
     * Some drivers will need to implement this method to free
     * database result resources. 
     * 
     * @return void
     */
    public function close();
    
    /**
     * Get result set.
     * This assumes that the last thing done was an executeQuery() or an execute()
     * with SELECT-type query.
     *
     * @return RestultSet Last ResultSet or <code>null</code> if not applicable.
     */
    public function getResultSet();
    
    /**
     * Gets next result set (if this behavior is supported by driver).
     * Some drivers (e.g. MSSQL) support returning multiple result sets -- e.g.
     * from stored procedures.
     *
     * This function also closes any current restult set.
     *
     * Default behavior is for this function to return false.  Driver-specific
     * implementations of this class can override this method if they actually
     * support multiple result sets.
     * 
     * @return boolean True if there is another result set, otherwise false.
     */
    public function getMoreResults();
    
    /**
     * Get update count.
     *
     * @return int Number of records affected, or <code>null</code> if not applicable.
     */
    public function getUpdateCount();

    /**
     * Sets the maximum number of rows to return from db.
     * This will affect the SQL if the RDBMS supports native LIMIT; if not,
     * it will be emulated.  Limit only applies to queries (not update sql).
     * @param int $v Maximum number of rows or 0 for all rows.
     * @return void
     */
    public function setLimit($v);
    
    /**
     * Returns the maximum number of rows to return or 0 for all.
     * @return int
     */
    public function getLimit();
    
    /**
     * Sets the start row.
     * This will affect the SQL if the RDBMS supports native OFFSET; if not,
     * it will be emulated. Offset only applies to queries (not update) and 
     * only is evaluated when LIMIT is set!
     * @param int $v
     * @return void
     */ 
    public function setOffset($v);
    
    /**
     * Returns the start row.
     * Offset only applies when Limit is set!
     * @return int
     */
    public function getOffset();
    
    /**
     * Executes the SQL query in this PreparedStatement object and returns the resultset generated by the query.
     * We support two signatures for this method:
     * - $stmt->executeQuery(ResultSet::FETCHMODE_NUM);
     * - $stmt->executeQuery(array($param1, $param2), ResultSet::FETCHMODE_NUM);
     * @param mixed $p1 Either (array) Parameters that will be set using PreparedStatement::set() before query is executed or (int) fetchmode.
     * @param int $fetchmode The mode to use when fetching the results (e.g. ResultSet::FETCHMODE_NUM, ResultSet::FETCHMODE_ASSOC).
     * @return ResultSet
     * @throws SQLException if a database access error occurs.
     */
    public function executeQuery();
    
    /**
     * Executes the SQL INSERT, UPDATE, or DELETE statement in this PreparedStatement object.
     * 
     * @param array $params Parameters that will be set using PreparedStatement::set() before query is executed.
     * @return int Number of affected rows (or 0 for drivers that return nothing).
     * @throws SQLException if a database access error occurs.
     */
    public function executeUpdate($params = null);

    /**
     * A generic set method.
     * 
     * You can use this if you don't want to concern yourself with the details.  It involves
     * slightly more overhead than the specific settesr, since it grabs the PHP type to determine
     * which method makes most sense.
     * 
     * @param int $paramIndex
     * @param mixed $value
     * @return void
     * @throws SQLException
     */
    public function set($paramIndex, $value);
    
    /**
     * Sets an array.
     * Unless a driver-specific method is used, this means simply serializing
     * the passed parameter and storing it as a string.
     * @param int $paramIndex
     * @param array $value
     * @return void
     */
    public function setArray($paramIndex, $value);

    /**
     * Sets a boolean value.
     * Default behavior is true = 1, false = 0.
     * @param int $paramIndex
     * @param boolean $value
     * @return void
     */
    public function setBoolean($paramIndex, $value);
    

    /**
     * @param int $paramIndex
     * @param mixed $blob Blob object or string containing data.
     * @return void
     */
    public function setBlob($paramIndex, $blob);

    /**
     * @param int $paramIndex
     * @param mixed $clob Clob object  or string containing data.
     * @return void
     */
    public function setClob($paramIndex, $clob);

    /**
     * @param int $paramIndex
     * @param string $value
     * @return void
     */
    public function setDate($paramIndex, $value);
        
    /**
     * @param int $paramIndex
     * @param float $value
     * @return void
     */
    public function setFloat($paramIndex, $value);

    /**
     * @param int $paramIndex
     * @param int $value
     * @return void
     */
    public function setInt($paramIndex, $value);

    /**
     * @param int $paramIndex
     * @return void
     */
    public function setNull($paramIndex);

    /**
     * @param int $paramIndex
     * @param string $value
     * @return void
     */
    public function setString($paramIndex, $value);
    
    /**
     * @param int $paramIndex
     * @param string $value
     * @return void
     */
    public function setTime($paramIndex, $value);
    
    /**
     * @param int $paramIndex
     * @param string $value
     * @return void
     */
    public function setTimestamp($paramIndex, $value);
            
}
