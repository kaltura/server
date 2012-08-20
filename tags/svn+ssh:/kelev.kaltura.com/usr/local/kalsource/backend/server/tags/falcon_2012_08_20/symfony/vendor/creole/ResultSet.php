<?php
/*
 *  $Id: ResultSet.php,v 1.28 2006/01/17 19:44:38 hlellelid Exp $
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
 * This is the interface for classes the wrap db results.
 * 
 * The get*() methods in this interface will format values before returning them. Note
 * that if they will return null if the database returned NULL.  If the requested column does
 * not exist than an exception (SQLException) will be thrown.
 * 
 * <code>
 * $rs = $conn->executeQuery("SELECT MAX(stamp) FROM event", ResultSet::FETCHMODE_NUM);
 * $rs->next();
 * 
 * $max_stamp = $rs->getTimestamp(1, "d/m/Y H:i:s");
 * // $max_stamp will be date string or null if no MAX(stamp) was found
 * 
 * $max_stamp = $rs->getTimestamp("max(stamp)", "d/m/Y H:i:s");
 * // will THROW EXCEPTION, because the resultset was fetched using numeric indexing
 * // SQLException: Invalid resultset column: max(stamp)
 * </code>
 * 
 * This class implements SPL IteratorAggregate, so you may iterate over the database results
 * using foreach():
 * <code>
 * foreach($rs as $row) {
 *   print_r($row); // row is assoc array returned by getRow()
 * }
 * </code>
 * 
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.28 $
 * @package   creole
 */
interface ResultSet extends IteratorAggregate {
        
    /**
     * Index result set by field name.
     */
    const FETCHMODE_ASSOC = 1;

    /**
     * Index result set numerically.
     */
    const FETCHMODE_NUM = 2;
    
    /**
     * Get the PHP native resource for the result.
     * Arguably this should not be part of the interface: i.e. every driver should implement
     * it if they have a result resource, but conceivably drivers could be created that do
     * not.  For now every single driver does have a "dblink" resource property, and other
     * classes (e.g. ResultSet) need this info in order to get correct native errors.  We'll
     * leave it in for now, as it helps with driver development, with the caveat that it 
     * could be removed from the interface at a later point.
     * @return resource Query result or NULL if not not applicable.
     */
    public function getResource();
        
    /**
     * Sets the fetchmode used to retrieve results.
     * Changing fetchmodes mid-result retrieval is supported (haven't encountered any drivers 
     * that don't support that yet).
     * @param int $mode ResultSet::FETCHMODE_NUM or  ResultSet::FETCHMODE_ASSOC (default).
     * @return void
     */
    public function setFetchmode($mode);
    
    /**
     * Gets the fetchmode used to retrieve results.
     * @return int ResultSet::FETCHMODE_NUM or ResultSet::FETCHMODE_ASSOC (default).
     */
    public function getFetchmode();       
    
    /**
     * Whether assoc result keys get converted to lowercase for compatibility.
     * 
     * This defaults to FALSE unless Creole::COMPAT_ASSOC_LOWER flag has been passed to connection.
     * This property is read-only since it must be set when connection is created.  The
     * reason for this behavior is some drivers (e.g. SQLite) do the case conversions internally
     * based on a PHP ini value; it would not be possible to change the behavior from the ResultSet
     * (since query has already been executed).
	 * 
     * @return boolean
     */
    public function isLowerAssocCase();
        
    /**
     * Moves the internal cursor to the next position and fetches the row at that position.
     * 
     * @return boolean <tt>true</tt> if success, <tt>false</tt> if no next record.
     * @throws SQLException on any driver-level errors.
     */
    public function next();

    /**
     * Moves the internal cursor to the previous position and fetches the
     * row at that position.
     * 
     * @return boolean <tt>true</tt> if success, <tt>false</tt> if no previous record.
     * @throws SQLException - if unable to move to previous position
     *                      - if ResultSet doesn't support reverse scrolling
     */
    public function previous();

    /**
     * Moves the cursor a relative number of rows, either positive or negative and fetches
     * the row at that position.
     * 
     * Attempting to move beyond the first/last row in the result set positions the cursor before/after 
     * the first/last row and issues a Warning. Calling relative(0) is valid, but does not change the cursor 
     * position. 
     * 
     * @param integer $offset
     * @return boolean <tt>true</tt> if cursor is on a row, <tt>false</tt> otherwise.
     * @throws SQLException - if unable to move to relative position
     *                      - if rel pos is negative & ResultSet doesn't support reverse scrolling
     */
    public function relative($offset);


    /**
     * Moves the cursor to an absolute cursor position and fetches the row at that position.
     * 
     * Attempting to move beyond the first/last row in the result set positions the cursor before/after 
     * the first/last row and issues a Warning.
     * 
     * @param integer $pos cursor position, first position is 1.
     * @return boolean <tt>true</tt> if cursor is on a row, <tt>false</tt> otherwise.
     * @throws SQLException - if unable to move to absolute position
     *                      - if position is before current pos & ResultSet doesn't support reverse scrolling
     */
    public function absolute($pos);

    /**
     * Moves cursor position WITHOUT FETCHING ROW AT THAT POSITION.
     * 
     * Generally this method is for internal driver stuff (e.g. other methods like
     * absolute() or relative() might call this and then call next() to get the row).
     * This method is public to facilitate more advanced ResultSet scrolling tools
     * -- e.g. cleaner implimentation of ResultSetIterator.
     * 
     * Some drivers will emulate seek() and not allow reverse seek (Oracle).
     * 
     * Seek is 0-based, but seek() is only for moving to the space _before_ the record
     * that you want to read.  I.e. if you seek(0) and then call next() you will have the 
     * first row (i.e. same as calling first() or absolute(1)).
     * 
     * <strong>IMPORTANT:  You cannot rely on the return value of this method to know whether a given
     * record exists for reading.  In some cases seek() will correctly return <code>false</code> if
     * the position doesn't exist, but in other drivers the seek is not performed until the
     * record is fetched. You can check the return value of absolute() if you need to know
     * whether a specific rec position is valid.</strong>
     * 
     * @param int $rownum The cursor pos to seek to.
     * @return boolean true on success, false if unable to seek to specified record.
     * @throws SQLException if trying to seek backwards with a driver that doesn't
     *                      support reverse-scrolling
     */
    public function seek($rownum);    
    
    /**
     * Move cursor to beginning of recordset.
     * @return boolean <tt>true</tt> on success or <tt>false</tt> if not found.
     * @throws SQLException - if unable to move to first position
     *                      - if not at first pos & ResultSet doesn't support reverse scrolling
     */
    public function first();

    /**
     * Move cursor to end of recordset.
     * @return boolean <tt>true</tt> on success or <tt>false</tt> if not found.
     * @throws SQLException - if unable to move to last position
     *                      - if unable to get num rows
     */
    public function last();
    
    /**
     * Sets cursort to before first record. This does not actually seek(), but
     * simply sets cursor pos to 0.
     * This is useful for inserting a record before the first in the set, etc.
     * @return void
     */
    public function beforeFirst();


    /**
     * Sets cursort to after the last record. This does not actually seek(), but
     * simply sets the cursor pos  to last + 1.
     * This [will be] useful for inserting a record after the last in the set,
     * when/if Creole supports updateable ResultSets.
     * @return void
     */
    public function afterLast();


    /**
     * Checks whether cursor is after the last record.
     * @return boolean
     * @throws SQLException on any driver-level error.
     */
    public function isAfterLast();

    /**
     * Checks whether cursor is before the first record.
     * @return boolean
     * @throws SQLException on any driver-level error.
     */
    public function isBeforeFirst();

    /**
     * Returns the current cursor position.
     * Cursor positions start at 0, but as soon as first row is fetched
     * cursor position is 1. (so first row is 1)
     * @return int
     */
    public function getCursorPos();

    /**
     * Gets current fields (assoc array).
     * @return array
     */
    public function getRow();

    /**
     * Get the number of rows in a result set.
     * @return int the number of rows
     * @throws SQLException - if unable to get a rowcount.
     */
    public function getRecordCount();
            
    /**
     * Frees the resources allocated for this result set.
     * Also empties any internal field array so that any calls to
     * get() method on closed ResultSet will result in "Invalid column" SQLException.
     * @return void
     */
    public function close();
    
    /**
     * A generic get method returns unformatted (=string) value.
     * This returns the raw results from the database.  Usually this will be a string, but some drivers
     * also can return objects (lob descriptors, etc) in certain cases.
     * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used) (if ResultSet::FETCHMODE_NUM was used).
     * @return mixed Usually expect a string.
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    public function get($column);
    
    /**
     * Reads a column as an array.
     * The value of the column is unserialized & returned as an array.  The generic case of this function is 
     * very PHP-specific.  Other drivers (e.g. Postgres) will format values into their native array format.
     * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used).
     * @return array value or null if database returned null.
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    public function getArray($column);

    /**
     * Returns value translated to boolean.
     * Default is to map 0 => false, 1 => true, but some database drivers may override this behavior.
     * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used).
     * @return boolean value or null if database returned null.
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    public function getBoolean($column);
            
    /**
     * Returns Blob with contents of column value.
     * 
     * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used).
     * @return Blob New Blob with data from column or null if database returned null.
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    public function getBlob($column);

    /**
     * Returns Clob with contents of column value.
     * 
     * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used).
     * @return Clob New Clob object with data from column or null if database returned null.
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    public function getClob($column);

    /**
     * Return a formatted date.
     * 
     * The default format for dates returned is preferred (in your locale, as specified using setlocale()) 
     * format w/o time (i.e. strftime("%x", $val)).  Override this by specifying a format second parameter.  You
     * can also specify a date()-style formatter; if you do, make sure there are no "%" symbols in your format string.
     * 
     * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used).
     * @param string $format Date formatter for use w/ strftime() or date() (it will choose based on examination of format string)
     *                          If format is NULL, then the integer unix timestamp will be returned (no formatting performed).
     * @return mixed  Formatted date, or integer unix timestamp (using 00:00:00 for time) if $format was null. 
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    public function getDate($column, $format = '%x');   

    /**
     * Returns value cast as a float (in PHP this is same as double).
     * 
     * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used).
     * @return float value or null if database returned null
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    public function getFloat($column);

    /**
     * Returns value cast as integer.
     * 
     * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used).
     * @return int value or null if database returned null
     * @see getInteger()
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    public function getInt($column);      

    /**
     * Returns value cast as string.
     * 
     * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used).
     * @return string value or null if database returned null
     * @see get()
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    public function getString($column);
    
    /**
     * Return a formatted time.
     * 
     * The default format for times returned is preferred (in your locale, as specified using setlocale()) 
     * format w/o date (i.e. strftime("%X", $val)).  Override this by specifying a format second parameter.  You
     * can also specify a date()-style formatter; if you do, make sure there are no "%" symbols in your format string.
     * 
     * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used).
     * @param string $format Date formatter for use w/ strftime() or date() (it will choose based on examination of format string)
     *                          If format is NULL, then the integer unix timestamp will be returned (no formatting performed).
     * @return mixed  Formatted time, or integer unix timestamp (using today's date) if $format was null. 
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    public function getTime($column, $format = '%X');

    /**
     * Return a formatted timestamp.
     * 
     * The default format for timestamp is ISO standard YYYY-MM-DD HH:MM:SS (i.e. date('Y-m-d H:i:s', $val).
     * Override this by specifying a format second parameter.  You can also specify a strftime()-style formatter.
     * 
     * Hint: if you want to get the unix timestamp use the "U" formatter string.
     * 
     * @param mixed $column Column name (string) or index (int) starting with 1 (if ResultSet::FETCHMODE_NUM was used).
     * @param string $format Date formatter for use w/ strftime() or date() (it will choose based on examination of format string)
     *                          If format is NULL, then the integer unix timestamp will be returned (no formatting performed).
     * @return mixed Formatted timestamp, or integer unix timestamp (if $format was null)
     * @throws SQLException - If the column specified is not a valid key in current field array.
     */
    public function getTimestamp($column, $format = 'Y-m-d H:i:s');
    
}

