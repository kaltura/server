<?php
/*
 *  $Id: ODBCAdapter.php,v 1.3 2005/10/17 19:03:51 dlawson_mi Exp $
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
 * Default class for ODBC driver-specific behavior.
 *
 * @author    Dave Lawson <dlawson@masterytech.com>
 * @version   $Revision: 1.3 $
 * @package   creole.drivers.odbc
 */
class ODBCAdapter
{
    /**
     * Returns true if column case is preserved in the database when a table
     * is first created. Returns false if table does not preserve case (i.e.
     * ProductID => PRODUCTID).
     *
     * @return boolean
     */
    public function preservesColumnCase()
    {
        return true;
    }

    /**
     * Returns true if prepared statements should be emulated. This
     * might be useful if your driver does not support (or has trouble with)
     * prepared statements.
     * 
     * @return boolean
     */
    public function emulatePrepareStmt()
    {
        return false;
    }
    
    /**
     * Returns true if ODBC driver supports LIMIT/OFFSET via SQL.
     *
     * @return boolean
     */
    public function hasLimitOffset()
    {
        return false;
    }

    /**
     * @see Connection::applyLimit()
     */
    public function applyLimit(&$sql, $offset, $limit)
    {
    }

    /**
     * @see PreparedStatementCommon::escape()
     */
    public function escape($str)
    {
        // use this instead of magic_quotes_sybase + addslashes(),
        // just in case multiple RDBMS being used at the same time
        return str_replace("'", "''", $str);
    }

    /**
     * Returns an instance of the default resultset.
     *
     * @return boolean
     */
    public function createResultSet($conn, $odbcresult, $fetchmode)
    {
        require_once 'creole/drivers/odbc/ODBCResultSet.php';
        return new ODBCResultSet($conn, $odbcresult, $fetchmode);
    }

    /**
     * Returns the default ODBCIdGenerator for emulating sequences.
     *
     * @return ODBCIdGenerator
     */
    public function getIdGenerator($conn)
    {
        require_once 'creole/drivers/odbc/ODBCIdGenerator.php';
        return new ODBCIdGenerator($conn);
    }

    /**
     * Returns true if driver support transactions.
     * 
     * @return boolean
     */
    public function supportsTransactions()
    {
        return true;
    }
}

?>