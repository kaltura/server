<?php
/*
 *  $Id: ODBCResultSet.php,v 1.2 2005/04/01 17:10:42 dlawson_mi Exp $
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

require_once 'creole/drivers/odbc/ODBCResultSetCommon.php';

/**
 * ODBC implementation of ResultSet.
 *
 * If the current ODBC driver does not support LIMIT or OFFSET natively,
 * the methods in here perform some adjustments and extra checking to make
 * sure that this behaves the same as RDBMS drivers using native OFFSET/LIMIT.
 *
 * This class also emulates a row count if the driver is not capable of 
 * providing one natively.
 * 
 * NOTE: This class only works with drivers that support absolute cursor 
 *       positioning (SQL_FETCH_DIRECTION = SQL_FD_FETCH_ABSOLUTE). If the
 *       driver you are using does not support reverse/absolute cursor 
 *       scrolling, you should use the {@link ODBCCachedResultSet} class instead.
 *       See the documentation for ODBCCachedResultSet for instructions on how
 *       to use it.
 *
 * @author    Dave Lawson <dlawson@masterytech.com>
 * @version   $Revision: 1.2 $
 * @package   creole.drivers.odbc
 */
class ODBCResultSet extends ODBCResultSetCommon implements ResultSet
{
    /**
     * Number of rows in resultset.
     *
     * @var int
     */
    protected $numRows = -1;

    /**
     * True if ODBC driver supports odbc_num_rows().
     *
     * @var boolean
     */
    protected $hasRowCount = false;
    
    /**
     * @see ResultSet::__construct()
     */
    public function __construct(Connection $conn, $result, $fetchmode = null)
    {
        parent::__construct($conn, $result, $fetchmode);

        /**
         * Some ODBC drivers appear not to handle odbc_num_rows() very well when
         * more than one result handle is active at once. For example, the MySQL
         * ODBC driver always returns the number of rows for the last executed
         * result. For this reason, we'll store the row count here.
         *
         * Note also that many ODBC drivers do not support this method. In this
         * case, getRecordCount() will perform a manual count.
         */
        $this->numRows = @odbc_num_rows($result->getHandle());
        $this->hasRowCount = $this->numRows != -1;
    }

    /**
     * @see ODBCResultSetCommon::close()
     */
    function close()
    {
        parent::close();
        $numRows = -1;
    }

    /**
     * @see ResultSet::seek()
     */
    public function seek($rownum)
    {
        if ($rownum < 0 || $this->limit > 0 && $rownum > $this->limit)
            return false;
        
        $this->cursorPos = $rownum;

        return true;
    }

    /**
     * @see ResultSet::next()
     */
    public function next()
    {
        $this->cursorPos++;
        
        if ($this->limit > 0 && $this->cursorPos > $this->limit) {
            $this->cursorPos = $this->limit+1;
            return false;
        }

        $rowNum = $this->offset + $this->cursorPos;
        $fields = null;
        
        $cols = @odbc_fetch_into($this->result->getHandle(), $fields, $rowNum);

        if ($cols === false) {
            $this->cursorPos = -1;
            return false;
        }

        $this->fields =& $this->checkFetchMode($fields);
        
        return true;
    }

    /**
     * @see ResultSet::isAfterLast()
     */
    public function isAfterLast()
    {
        // Force calculation of last record pos.
        if ($this->cursorPos == -1)
            $this->getRecordCount();
            
        return parent::isAfterLast();
    }

    /**
     * @see ResultSet::getRecordCount()
     */
    function getRecordCount()
    {
        if ($this->hasRowCount)
        {
            // Use driver row count if provided.
            $numRows = $this->numRows - $this->offset;

            if ($this->limit > 0 && $numRows > $this->limit)
                $numRows = $this->limit;
        }
        else 
        {
            // Do manual row count if driver doesn't provide one.
            if ($this->numRows == -1) 
            {
                $this->numRows = 0;
                $this->beforeFirst();
            
                while($this->next()) 
                    $this->numRows++;
            }
                
            $numRows = $this->numRows;
        }

        // Cursor pos is -1 when an attempt to fetch past the last row was made
        // (or a fetch error occured).
        
        if ($this->cursorPos == -1)
            $this->cursorPos = $numRows+1;
            
        return $numRows;
    }

    /**
     * @see ResultSet::getBlob()
     */
    public function getBlob($column)
    {
        require_once 'creole/util/Blob.php';
        $idx = (is_int($column) ? $column - 1 : $column);
        if (!array_key_exists($idx, $this->fields)) { throw new SQLException("Invalid resultset column: " . $column); }
        $data = $this->readLobData($column, ODBC_BINMODE_RETURN, $this->fields[$idx]);
        if (!$data) { return null; }
        $b = new Blob();
        $b->setContents($data);
        return $b;
    }

    /**
     * @see ResultSet::getClob()
     */
    public function getClob($column)
    {
        require_once 'creole/util/Clob.php';
        $idx = (is_int($column) ? $column - 1 : $column);
        if (!array_key_exists($idx, $this->fields)) { throw new SQLException("Invalid resultset column: " . $column); }
        $data = $this->readLobData($column, ODBC_BINMODE_CONVERT, $this->fields[$idx]);
        if (!$data) { return null; }
        $c = new Clob();
        $c->setContents($data);
        return $c;
    }

}