<?php
/*
 *  $Id: PgSQLResultSet.php,v 1.31 2006/01/17 19:44:40 hlellelid Exp $
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
 * PostgreSQL implementation of ResultSet.
 *
 * @author	Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.31 $
 * @package   creole.drivers.pgsql
 */
class PgSQLResultSet extends ResultSetCommon implements ResultSet {


	/**
	 * Gets optimized PgSQLResultSetIterator.
	 * @return PgSQLResultSetIterator
	 */
	/*
	public function getIterator()
	{   
		require_once 'creole/drivers/pgsql/PgSQLResultSetIterator.php';
		return new PgSQLResultSetIterator($this);
	}
	*/

	/**
	 * Postgres doesn't actually move the db pointer.  The specific row
	 * is fetched by call to pg_fetch_array() rather than by a seek and
	 * then an unspecified pg_fetch_array() call.
	 * 
	 * The only side-effect of this situation is that we don't really know 
	 * if the seek will fail or succeed until we have called next().  This
	 * behavior is acceptible - and explicitly documented in 
	 * ResultSet::seek() phpdoc.
	 * 
	 * @see ResultSet::seek()
	 */ 
	public function seek($rownum)
	{
		if ($rownum < 0) {
			return false;
		}
		
		// PostgreSQL rows start w/ 0, but this works, because we are
		// looking to move the position _before_ the next desired position
		$this->cursorPos = $rownum;
		return true;
	}
	
	/**
	 * @see ResultSet::next()
	 */ 
	public function next()
	{

		$this->fields = @pg_fetch_array($this->result, $this->cursorPos, $this->fetchmode);

		if (!$this->fields) {
			$err = @pg_result_error($this->result);
			if (!$err) {
				// We've advanced beyond end of recordset.
				$this->afterLast();
				return false;
			} else {
				throw new SQLException("Error fetching result", $err);				
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
	public function getRecordCount()
	{
		$rows = @pg_num_rows($this->result);
		if ($rows === null) {
			throw new SQLException("Error fetching num rows", pg_result_error($this->result));
		}
		return (int) $rows;
	}	

	/**
	 * @see ResultSet::close()
	 */
	public function close()
	{
		$this->fields = array();
		@pg_free_result($this->result);
	}
		
	/**
	 * Convert Postgres string representation of array into native PHP array.
	 * @param string $str Postgres string array rep: {1223, 2343} or {{"welcome", "home"}, {"test2", ""}}
	 * @return array
	 */
	private function strToArray($str)
	{
		$str = substr($str, 1, -1); // remove { }
		$res = array();
		
		$subarr = array();
		$in_subarr = 0;
		
		$toks = explode(',', $str);
		foreach($toks as $tok) {					
			if ($in_subarr > 0) { // already in sub-array?
				$subarr[$in_subarr][] = $tok;
				if ('}' === substr($tok, -1, 1)) { // check to see if we just added last component					
					$res[] = $this->strToArray(implode(',', $subarr[$in_subarr]));
					$in_subarr--;
				}
			} elseif ($tok{0} === '{') { // we're inside a new sub-array							   
				if ('}' !== substr($tok, -1, 1)) {
					$in_subarr++;
					// if sub-array has more than one element
					$subarr[$in_subarr] = array();
					$subarr[$in_subarr][] = $tok;					
				} else {
					$res[] = $this->strToArray($tok);
				}
			} else { // not sub-array
				$val = trim($tok, '"'); // remove " (surrounding strings)
				// perform type castng here?
				$res[] = $val;
			}
		}
		
		return $res;
	}

	/**
	 * Reads a column as an array.
	 * The value of the column is unserialized & returned as an array.
	 * @param mixed $column Column name (string) or index (int) starting with 1.
	 * @return array
	 * @throws SQLException - If the column specified is not a valid key in current field array.
	 */
	public function getArray($column) 
	{
		if (is_int($column)) { $column--; } // because Java convention is to start at 1 
		if (!array_key_exists($column, $this->fields)) { throw new SQLException("Invalid resultset column: " . (is_int($column) ? $column + 1 : $column)); }
		if ($this->fields[$column] === null) { return null; }
		return $this->strToArray($this->fields[$column]);
	} 
	
	/**
	 * Returns Blob with contents of column value.
	 * 
	 * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used).
	 * @return Blob New Blob with data from column.
	 * @throws SQLException - If the column specified is not a valid key in current field array.
	 */
	public function getBlob($column) 
	{
		if (is_int($column)) { $column--; } // because Java convention is to start at 1 
		if (!array_key_exists($column, $this->fields)) { throw new SQLException("Invalid resultset column: " . (is_int($column) ? $column + 1 : $column)); }
		if ($this->fields[$column] === null) { return null; }
		require_once 'creole/util/Blob.php';
		$b = new Blob();
		$b->setContents(pg_unescape_bytea($this->fields[$column]));
		return $b;
	}	 

	/**
	 * @param mixed $column Column name (string) or index (int) starting with 1.
	 * @return boolean
	 * @throws SQLException - If the column specified is not a valid key in current field array.
	 */
	public function getBoolean($column) 
	{
		if (is_int($column)) { $column--; } // because Java convention is to start at 1 
		if (!array_key_exists($column, $this->fields)) { throw new SQLException("Invalid resultset column: " . (is_int($column) ? $column + 1 : $column)); }
		if ($this->fields[$column] === null) { return null; }
		return ($this->fields[$column] === 't');
	}

}
