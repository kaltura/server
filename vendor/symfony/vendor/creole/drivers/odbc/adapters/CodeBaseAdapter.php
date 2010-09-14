<?php
/*
 *  $Id: CodeBaseAdapter.php,v 1.3 2005/10/17 19:03:51 dlawson_mi Exp $
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

require_once 'creole/drivers/odbc/adapters/ODBCAdapter.php';

/**
 * CodeBase driver-specific behavior.
 *
 * This adapter is for Sequiter's CodeBaseSQL product. It is a dBase ODBC
 * driver. The driver only supports forward-only cursor scrolling so this
 * adapter causes the ODBCCachedResultSet to be used.
 *
 * A couple other quirks exist: 
 * 
 * 1) Cannot get blobs to work correctly. If I try writing one to a 
 *    LONGVARBINARY typed field, only the first few bytes are written.
 *    This will cause the ResultSetTest::testGetBlob() test case to fail
 *    when running tests for the driver.
 *
 * 2) For some reason the character count is off for the 
 *    ResultSetTest::testSetClob() test case _only_ when running from the 
 *    command line. If I run the same test through a web server it works fine.
 *    Looks like it has something to do with line endings in Windows. The 
 *    difference in file sizes is 9803 vs 10090.
 *
 * 3) Setting a clob field to null writes a space to the field in the table. 
 *    This causes the PreparedStatementTest::testSetNull() test case to fail 
 *    when running tests for the driver.
 *
 * @author    Dave Lawson <dlawson@masterytech.com>
 * @version   $Revision: 1.3 $
 * @package   creole.drivers.odbc
 */
class CodeBaseAdapter extends ODBCAdapter
{
    /**
     * @see ODBCAdapter::createResultSet()
     */
    public function preservesColumnCase()
    {
        return false;
    }

    /**
     * @see ODBCAdapter::createResultSet()
     */
    public function createResultSet($conn, $odbcresult, $fetchmode)
    {
        require_once 'creole/drivers/odbc/ODBCResultSet.php';
        return new ODBCResultSet($conn, $odbcresult, $fetchmode, true);
    }

}

?>