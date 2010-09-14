<?php
/*
 *  $Id: SQLiteResultSetIterator.php,v 1.6 2004/12/03 16:57:54 gamr Exp $
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
 * Optimized iterator for SQLite.
 * 
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.6 $
 * @package   creole.drivers.sqlite
 */
class SQLiteResultSetIterator implements Iterator {

    private $result;
    private $pos = 0;
    private $fetchmode;
    private $row_count;
    
    /**
     * Construct the iterator.
     * @param SQLiteResultSet $rs
     */
    public function __construct(SQLiteResultSet $rs)
    {
        $this->result = $rs->getResource();
        $this->fetchmode = $rs->getFetchmode();
	$this->row_count = $rs->getRecordCount();
    }
    
    /**
     * This method actually has no effect, since we do not rewind ResultSet for iteration.
     */
    function rewind()
    {        
        sqlite_rewind($this->result);
    }
    
    function valid()
    {
	return ( $this->pos < $this->row_count );
    }
    
    /**
     * Returns the cursor position.  Note that this will not necessarily
     * be 1 for the first row, since no rewind is performed at beginning
     * of iteration.
     * @return int
     */
    function key()
    {
        return $this->pos;
    }
    
    /**
     * Returns the row (assoc array) at current cursor pos.
     * @return array
     */
    function current()
    {
       return sqlite_fetch_array($this->result, $this->fetchmode);
    }
    
    /**
     * Advances internal cursor pos.
     */
    function next()
    {
        $this->pos++;
    }

}
