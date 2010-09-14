<?php
/*
 *  $Id: ODBCResultSetCommon.php,v 1.3 2006/01/17 19:44:39 hlellelid Exp $
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

require_once 'creole/ResultSet.php';
require_once 'creole/common/ResultSetCommon.php';

/**
 * Base class for ODBC implementation of ResultSet.
 *
 * @author    Dave Lawson <dlawson@masterytech.com>
 * @version   $Revision: 1.3 $
 * @package   creole.drivers.odbc
 */
abstract class ODBCResultSetCommon extends ResultSetCommon
{
    /**
     * Offset at which to start reading rows (for emulated offset).
     * @var int
     */
    protected $offset = 0;

    /**
     * Maximum rows to retrieve, or 0 if all (for emulated limit).
     * @var int
     */
    protected $limit = 0;

    /**
     * @see ResultSet::__construct()
     */
    public function __construct(Connection $conn, $result, $fetchmode = null)
    {
        parent::__construct($conn, $result, $fetchmode);
    }

    /**
     * @see ResultSet::close()
     */
    public function close()
    {
        $this->result = null;
        $this->conn = null;
        $this->fetchmode = null;
        $this->cursorPos = 0;
        $this->fields = null;
        $this->lowerAssocCase = false;
        $this->limit = 0;
        $this->offset = 0;
    }

    /**
     * This function exists to set offset after ResultSet is instantiated.
     * This function should be "protected" in Java sense: only available to classes in package.
     * THIS METHOD SHOULD NOT BE CALLED BY ANYTHING EXCEPTION DRIVER CLASSES.
     * @param int $offset New offset.
     * @access protected
     */
    public function _setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * This function exists to set limit after ResultSet is instantiated.
     * This function should be "protected" in Java sense: only available to classes in package.
     * THIS METHOD SHOULD NOT BE CALLED BY ANYTHING EXCEPTION DRIVER CLASSES.
     * @param int $limit New limit.
     * @access protected
     */
    public function _setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * If fetchmode is FETCHMODE_ASSOC, returns the 1-based field index number
     * for the specified column name. Otherwise returns 0 (false).
     * @return int
     */
    function getFieldNum($colname)
    {
        $fieldnum = 0;

        if ($this->fetchmode == ResultSet::FETCHMODE_ASSOC)
        {
            $keys = array_keys($this->fields);
            $fieldnum = array_search($colname, $keys);
        }

        return $fieldnum;
    }

    /**
     * Reads in any unread LOB data. For long char fields, we may already
     * have up to odbc_longreadlen() bytes in the buffer. These are passed
     * in via the $curdata parm. For long binary fields, no data is read
     * initially since odbc_binmode() is set to ODBC_BINMODE_PASSTHRU.
     * This method adjusts the binmode and longreadlen to finish reading
     * these datatypes into the buffer. Returns a string with the complete
     * contents.
     *
     * @param int|string $column Column index or name to read data from.
     * @param int $binmode ODBC_BINMODE_RETURN for binary data, ODBC_BINMODE_CONVERT for char data.
     * @param string $curdata Existing LOB data already in buffer.
     * @return string
     */
    protected function readLobData($column, $binmode, $curdata = null)
    {
        // Retrieve field num
        $fldNum = (is_int($column) ? $column : getFieldNum($column));

        $data = $curdata;
        $newdata = null;

        // Adjust binmode and longreadlen
        odbc_binmode($this->result->getHandle(), $binmode);
        odbc_longreadlen($this->result->getHandle(), 4096);

        while (1)
        {
            $newdata = odbc_result($this->result->getHandle(), $fldNum);

            if ($newdata === false)
                break;
            else
                $data .= $newdata;
        }

        // Restore the default binmode and longreadlen
        odbc_binmode($this->result->getHandle(), ODBC_BINMODE_PASSTHRU);
        odbc_longreadlen($this->result->getHandle(), ini_get('odbc.defaultlrl'));

        // The ODBC driver I use seems to return a string with an escaped
        // null char at the end for clob data.
        $data = rtrim($data, "\x0");

        return $data;
    }
    
    /**
     * Converts row fields to names if FETCHMODE_ASSOC is set.
     *
     * @param array& Row to convert.
     *
     * @return array& Converted row.
     */
    protected function checkFetchMode(&$row)
    {
        if ($this->fetchmode == ResultSet::FETCHMODE_ASSOC)
        {
            $newrow = array();
            
            for ($i = 0, $n = count($row); $i < $n; $i++)
            {
                $colname = @odbc_field_name($this->result->getHandle(), $i+1);
                
                if ($this->lowerAssocCase) {
                    $colname = strtolower($colname);
                }
				
                $newrow[$colname] = $row[$i];
            }
            
            $row =& $newrow;
        }
        
        return $row;
    }

}