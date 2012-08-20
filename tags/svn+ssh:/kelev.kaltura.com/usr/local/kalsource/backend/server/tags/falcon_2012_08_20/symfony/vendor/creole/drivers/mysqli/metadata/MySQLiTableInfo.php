<?php
/*
 * $Id: MySQLiTableInfo.php,v 1.3 2006/01/17 19:44:39 hlellelid Exp $
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
 * MySQLi implementation of TableInfo.
 *
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @version   $Revision: 1.3 $
 * @package   creole.drivers.mysqli.metadata
 */
class MySQLiTableInfo extends TableInfo {
    /** Loads the columns for this table. */
    protected function initColumns()
    {
        require_once 'creole/metadata/ColumnInfo.php';
        require_once 'creole/drivers/mysql/MySQLTypes.php';

        if (!@mysqli_select_db($this->conn->getResource(), $this->dbname)) {
            throw new SQLException('No database selected');
        }

        // To get all of the attributes we need, we use
        // the MySQL "SHOW COLUMNS FROM $tablename" SQL.
        $res = mysqli_query($this->conn->getResource(), "SHOW COLUMNS FROM " . $this->name);

        $defaults = array();
        $nativeTypes = array();
        $precisions = array();

        while($row = mysqli_fetch_assoc($res)) {
            $name = $row['Field'];
            $default = $row['Default'];
            $is_nullable = ($row['Null'] == 'YES');

            $size = null;
            $precision = null;
            $scale = null;

            if (preg_match('/^(\w+)[\(]?([\d,]*)[\)]?( |$)/', $row['Type'], $matches)) {
                //            colname[1]   size/precision[2]
                $nativeType = $matches[1];
                if ($matches[2]) {
                    if ( ($cpos = strpos($matches[2], ',')) !== false) {
                        $size = (int) substr($matches[2], 0, $cpos);
                        $precision = $size;
                        $scale = (int) substr($matches[2], $cpos + 1);
                    } else {
                        $size = (int) $matches[2];
                    }
                }
            } elseif (preg_match('/^(\w+)\(/', $row['Type'], $matches)) {
                $nativeType = $matches[1];
            } else {
                $nativeType = $row['Type'];
            }

            $this->columns[$name] = new ColumnInfo($this, $name, MySQLTypes::getType($nativeType), $nativeType, $size, $precision, $scale, $is_nullable, $default);
        }

        $this->colsLoaded = true;
    }

    /** Loads the primary key information for this table. */
    protected function initPrimaryKey()
    {
        require_once 'creole/metadata/PrimaryKeyInfo.php';

        // columns have to be loaded first
        if (!$this->colsLoaded) {
            $this->initColumns();
        }

        if (!@mysqli_select_db($this->conn->getResource(), $this->dbname)) {
            throw new SQLException('No database selected');
        }

        // Primary Keys
        $res = mysqli_query($this->conn->getResource(), "SHOW KEYS FROM " . $this->name);

        // Loop through the returned results, grouping the same key_name together
        // adding each column for that key.
        while($row = mysqli_fetch_assoc($res)) {
            $name = $row["Column_name"];
            if (!isset($this->primaryKey)) {
                $this->primaryKey = new PrimaryKeyInfo($name);
            }

            $this->primaryKey->addColumn($this->columns[ $name ]);
        }

        $this->pkLoaded = true;
    }

    /** Loads the indexes for this table. */
    protected function initIndexes() {
        require_once 'creole/metadata/IndexInfo.php';

        // columns have to be loaded first
        if (!$this->colsLoaded) {
            $this->initColumns();
        }

        if (!@mysqli_select_db($this->conn->getResource(), $this->dbname)) {
            throw new SQLException('No database selected');
        }

        // Indexes
        $res = mysqli_query($this->conn->getResource(), "SHOW INDEX FROM " . $this->name);

        // Loop through the returned results, grouping the same key_name together
        // adding each column for that key.
        while($row = mysqli_fetch_assoc($res)) {
            $name = $row["Column_name"];

            if (!isset($this->indexes[$name])) {
                $this->indexes[$name] = new IndexInfo($name);
            }

            $this->indexes[$name]->addColumn($this->columns[ $name ]);
        }

        $this->indexesLoaded = true;
    }

    /** Load foreign keys (unsupported in MySQL). */
    protected function initForeignKeys() {
        // columns have to be loaded first
        if (!$this->colsLoaded) {
            $this->initColumns();
        }

        // Foreign keys are not supported in mysql.
        $this->fksLoaded = true;
    }
}
