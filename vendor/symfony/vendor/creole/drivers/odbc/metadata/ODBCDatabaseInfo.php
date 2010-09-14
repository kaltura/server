<?php
/*
 *  $Id: ODBCDatabaseInfo.php,v 1.2 2006/01/17 19:44:39 hlellelid Exp $
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

require_once 'creole/metadata/DatabaseInfo.php';

/**
 * ODBC implementation of DatabaseInfo.
 *
 * @todo Still need a way to obtain the database name. Not sure how to do this yet.
 * @todo This might need to be an {@link ODBCAdapter} method.
 *
 * @author    Dave Lawson <dlawson@masterytech.com>
 * @version   $Revision: 1.2 $
 * @package   creole.drivers.odbc.metadata
 */
class ODBCDatabaseInfo extends DatabaseInfo {

    /**
     * @see DatabaseInfo::initTables()
     */
    protected function initTables()
    {
        include_once 'creole/drivers/odbc/metadata/ODBCTableInfo.php';

        $result = @odbc_tables($this->conn->getResource());

        if (!$result)
            throw new SQLException('Could not list tables', $this->conn->nativeError());

        while (odbc_fetch_row($result))
        {
            $tablename = strtoupper(odbc_result($result, 'TABLE_NAME'));
            $this->tables[$tablename] = new ODBCTableInfo($this, $tablename);
        }

        @odbc_free_result($result);
    }

    /**
     * @return void
     * @throws SQLException
     */
    protected function initSequences()
    {
        // Not sure how this is used yet.
    }

}