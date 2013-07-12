<?php
/*
 *  $Id: ODBCTableInfo.php,v 1.2 2006/01/17 19:44:39 hlellelid Exp $
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
 
require_once 'creole/metadata/TableInfo.php';

/**
 * ODBC implementation of TableInfo.
 *
 * @author    Dave Lawson <dlawson@masterytech.com>
 * @version   $Revision: 1.2 $
 * @package   creole.drivers.odbc.metadata
 */
class ODBCTableInfo extends TableInfo {

    /**
     * @see TableInfo::initColumns()
     */
    protected function initColumns()
    {
        include_once 'creole/metadata/ColumnInfo.php';
        include_once 'creole/drivers/odbc/ODBCTypes.php';

        ODBCTypes::loadTypeMap($this->conn);

        $result = @odbc_columns($this->conn->getResource(), $this->dbname, '', $this->name);

        if (!$result)
            throw new SQLException('Could not get column names', $this->conn->nativeError());

        while (odbc_fetch_row($result))
        {
            $name = odbc_result($result, 'COLUMN_NAME');
            $type = odbc_result($result, 'TYPE_NAME');
            $length = odbc_result($result, 'LENGTH');
            $is_nullable = odbc_result($result, 'NULLABLE');
            $default = '';
            $precision = odbc_result($result, 'PRECISION');
            $scale = odbc_result($result, 'SCALE');
            $this->columns[$name] = new ColumnInfo($this, $name, ODBCTypes::getType($type), $type, $length, $precision, $scale, $is_nullable, $default);
        }

        @odbc_free_result($result);

        $this->colsLoaded = true;
    }

    /**
     * @see TableInfo::initPrimaryKey()
     */
    protected function initPrimaryKey()
    {
        include_once 'creole/metadata/PrimaryKeyInfo.php';

        // columns have to be loaded first
        if (!$this->colsLoaded) $this->initColumns();

        $result = @odbc_primarykeys($this->conn->getResource(), $this->dbname, '', $this->name);

        while (odbc_fetch_row($result))
        {
            $name = odbc_result($result, 'COLUMN_NAME');

            if (!isset($this->primaryKey))
                $this->primaryKey = new PrimaryKeyInfo($name);

            $this->primaryKey->addColumn($this->columns[$name]);
        }

        @odbc_free_result($result);

        $this->pkLoaded = true;
    }

    /**
     * @see TableInfo::initIndexes()
     */
    protected function initIndexes()
    {
        // Not sure if this can be implemented in a driver-independent way.
    }

    /**
     * @see TableInfo::initForeignKeys()
     */
    protected function initForeignKeys()
    {
        // columns have to be loaded first
        if (!$this->colsLoaded) $this->initColumns();

        $result = @odbc_foreignkeys($this->conn->getResource(), '', '', '', $this->dbname, '', $this->name);

        while (odbc_fetch_row($result))
        {
            $name = odbc_result($result, 'COLUMN_NAME');
            $ftbl = odbc_result($result, 'FKTABLE_NAME');
            $fcol = odbc_result($result, 'FKCOLUMN_NAME');

            if (!isset($this->foreignKeys[$name]))
            {
                $this->foreignKeys[$name] = new ForeignKeyInfo($name);

                if (($foreignTable = $this->database->getTable($ftbl)) === null)
                {
                    $foreignTable = new TableInfo($ltbl);
                    $this->database->addTable($foreignTable);
                }

                if (($foreignCol = $foreignTable->getColumn($name)) === null)
                {
                    $foreignCol = new ColumnInfo($foreignTable, $name);
                    $foreignTable->addColumn($foreignCol);
                }

                $this->foreignKeys[$name]->addReference($this->columns[$name], $foreignCol);
            }
        }

        @odbc_free_result($result);

        $this->fksLoaded = true;
    }

}