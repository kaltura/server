<?php
/*
 *  $Id: MSSQLResultSet.php,v 1.21 2006/01/17 19:44:38 hlellelid Exp $
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
 * MSSQL implementation of ResultSet.
 *
 * MS SQL does not support LIMIT or OFFSET natively so the methods
 * in here need to perform some adjustments and extra checking to make sure
 * that this behaves the same as RDBMS drivers using native OFFSET/LIMIT.
 * 
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.21 $
 * @package   creole.drivers.mssql
 */
class MSSQLResultSet extends ResultSetCommon implements ResultSet {    
    
    /**
     * Offset at which to start reading rows.
     * @var int
     */
    private $offset = 0;
    
    /**
     * Maximum rows to retrieve, or 0 if all.
     * @var int
     */
    private $limit = 0;   
    
    /**
     * This MSSQL-only function exists to set offset after ResultSet is instantiated.
     * This function should be "protected" in Java sense: only available to classes in package.
     * THIS METHOD SHOULD NOT BE CALLED BY ANYTHING EXCEPTION DRIVER CLASSES.
     * @param int $offset New offset.  If great than 0, then seek(0) will be called to move cursor.
     * @access protected
     */
    public function _setOffset($offset)
    {
        $this->offset = $offset;
        if ($offset > 0) {
            $this->seek(0);  // 0 becomes $offset by seek() method
        }
    }
    
    /**
     * This MSSQL-only function exists to set limit after ResultSet is instantiated.
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
     * @see ResultSet::seek()
     */ 
    function seek($rownum)
    {
        // support emulated OFFSET
        $actual = $rownum + $this->offset;
        
        if (($this->limit > 0 && $rownum >= $this->limit) || $rownum < 0) {
                    // have to check for rownum < 0, because mssql_seek() won't
                    // complain if the $actual is valid.
            return false;
        }
                
        // MSSQL rows start w/ 0, but this works, because we are
        // looking to move the position _before_ the next desired position
         if (!@mssql_data_seek($this->result, $actual)) {
                return false;
        }

        $this->cursorPos = $rownum;
        return true;
    }
    
    /**
     * @see ResultSet::next()
     */
    function next()
    {
        // support emulated LIMIT
        if ( $this->limit > 0 && ($this->cursorPos >= $this->limit) ) {
            $this->afterLast();
            return false;
        }
        
        $this->fields = mssql_fetch_array($this->result, $this->fetchmode);        
                
        if (!$this->fields) {
            if ($errmsg = mssql_get_last_message()) {
                throw new SQLException("Error fetching result", $errmsg);
             } else {
                // We've advanced beyond end of recordset.
                $this->afterLast();
                return false;
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
        $rows = @mssql_num_rows($this->result);
        if ($rows === null) {
            throw new SQLException('Error getting record count', mssql_get_last_message());
        }
        // adjust count based on emulated LIMIT/OFFSET
        $rows -= $this->offset;
        return ($this->limit > 0 && $rows > $this->limit ? $this->limit : $rows);
    }

    /**
     * @see ResultSet::close()
     */ 
    function close()
    {
        $ret = @mssql_free_result($this->result);
        $this->result = false;
        $this->fields = array();
        $this->limit = 0;
        $this->offset = 0;        
    }   

}
