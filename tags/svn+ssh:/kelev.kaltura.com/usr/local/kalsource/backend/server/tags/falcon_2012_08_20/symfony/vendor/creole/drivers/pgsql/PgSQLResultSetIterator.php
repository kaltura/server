<?php
/*
 *  $Id: PgSQLResultSetIterator.php,v 1.1 2004/12/04 05:58:53 gamr Exp $
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
 * Optimized iterator for PostgreSQL, based off of SQLite iterator.
 * Testing with SeekableIterator, no idea if it will keep this
 * functionality or what uses it or even how to use it as yet.
 * 
 * @author    Cameron Brunner <webmaster@animetorrents.com>
 * @version   $Revision: 1.1 $
 * @package   creole.drivers.pgsql
 */
class PgSQLResultSetIterator implements SeekableIterator, Countable {

    private $result;
    private $pos = 0;
    private $fetchmode;
    private $row_count;
    private $rs;
    
    /**
     * Construct the iterator.
     * @param PgSQLResultSet $rs
     */
    public function __construct(PgSQLResultSet $rs)
    {
        $this->result = $rs->getResource();
        $this->fetchmode = $rs->getFetchmode();
		$this->row_count = $rs->getRecordCount();
		$this->rs = $rs; // This is to address reference count bug: http://creole.phpdb.org/trac/ticket/6
    }
    
    /**
     * This method actually has no effect, since we do not rewind ResultSet for iteration.
     */
    function rewind()
    {        
        $this->pos = 0;
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
       return pg_fetch_array($this->result, $this->pos, $this->fetchmode);
    }
    
    /**
     * Advances internal cursor pos.
     */
    function next()
    {
        $this->pos++;
    }

    /**
     * Sets cursor to specific value.
     */
    function seek ( $index )
    {
    	if ( ! is_int ( $index ) ) {
			throw new InvalidArgumentException ( 'Invalid arguement to seek' );
		}
		if ( $index < 0 || $index > $this->row_count ) {
			throw new OutOfBoundsException ( 'Invalid seek position' );
		}
		$this->pos = $index;
    }

    function count ( ) {
		return $this->row_count;
    }
}
