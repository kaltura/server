<?php
/*
 *  $Id: MySQLResultSet.php,v 1.24 2006/01/17 19:44:39 hlellelid Exp $
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
 * MySQL implementation of ResultSet class.
 *
 * MySQL supports OFFSET / LIMIT natively; this means that no adjustments or checking
 * are performed.  We will assume that if the lmitSQL() operation failed that an
 * exception was thrown, and that OFFSET/LIMIT will never be emulated for MySQL.
 * 
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.24 $
 * @package   creole.drivers.mysql
 */
class MySQLResultSet extends ResultSetCommon implements ResultSet {

    /**
     * @see ResultSet::seek()
     */ 
    public function seek($rownum)
    {
        // MySQL rows start w/ 0, but this works, because we are
        // looking to move the position _before_ the next desired position
         if (!@mysql_data_seek($this->result, $rownum)) {
                return false;
        }
        $this->cursorPos = $rownum;
        return true;
    }
    
    /**
     * @see ResultSet::next()
     */ 
    public function next()
    {
        $this->fields = mysql_fetch_array($this->result, $this->fetchmode);        

           if (!$this->fields) {
            $errno = mysql_errno($this->conn->getResource());
            if (!$errno) {
                // We've advanced beyond end of recordset.
                $this->afterLast();
                return false;
            } else {
                throw new SQLException("Error fetching result", mysql_error($this->conn->getResource()));
            }
        }
        
        if ($this->fetchmode === ResultSet::FETCHMODE_ASSOC && $this->lowerAssocCase) {
            $this->fields = array_change_key_case($this->fields, CASE_LOWER);
        }
        
        // Advance cursor position
        $this->cursorPos++;                
        return true;
    }

    /**
     * @see ResultSet::getRecordCount()
     */
    function getRecordCount()
    {
        $rows = @mysql_num_rows($this->result);
        if ($rows === null) {
            throw new SQLException("Error fetching num rows", mysql_error($this->conn->getResource()));
        }
        return (int) $rows;
    }

    /**
     * @see ResultSet::close()
     */ 
    function close()
    {        
        if(is_resource($this->result))
            @mysql_free_result($this->result);
        $this->fields = array();
    }    
        
    /**
     * Get string version of column.
     * No rtrim() necessary for MySQL, as this happens natively.
     * @see ResultSet::getString()
     */
    public function getString($column) 
    {
        $idx = (is_int($column) ? $column - 1 : $column);
        if (!array_key_exists($idx, $this->fields)) { throw new SQLException("Invalid resultset column: " . $column); }
        if ($this->fields[$idx] === null) { return null; }
        return (string) $this->fields[$idx];
    }
    
    /**
     * Returns a unix epoch timestamp based on either a TIMESTAMP or DATETIME field.
     * @param mixed $column Column name (string) or index (int) starting with 1.
     * @return string
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    function getTimestamp($column, $format='Y-m-d H:i:s') 
    {
        if (is_int($column)) { $column--; } // because Java convention is to start at 1 
        if (!array_key_exists($column, $this->fields)) { throw new SQLException("Invalid resultset column: " . (is_int($column) ? $column + 1 : $column)); }
        if ($this->fields[$column] === null) { return null; }
        
        $ts = strtotime($this->fields[$column]);
        if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
            // otherwise it's an ugly MySQL timestamp!
            // YYYYMMDDHHMMSS
            if (preg_match('/([\d]{4})([\d]{2})([\d]{2})([\d]{2})([\d]{2})([\d]{2})/', $this->fields[$column], $matches)) {
                //              YYYY      MM        DD      HH        MM       SS
                //                $1        $2          $3      $4        $5         $6
                $ts = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);        
            }
        }
        if ($ts === -1 || $ts === false) { // if it's still -1, then there's nothing to be done; use a different method.
            throw new SQLException("Unable to convert value at column " . (is_int($column) ? $column + 1 : $column) . " to timestamp: " . $this->fields[$column]);
        }        
        if ($format === null) {
            return $ts;
        }
        if (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

}
