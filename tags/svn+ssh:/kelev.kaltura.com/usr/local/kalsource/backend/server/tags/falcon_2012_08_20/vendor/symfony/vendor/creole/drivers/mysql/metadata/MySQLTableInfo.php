<?php
/*
 *  $Id: MySQLTableInfo.php,v 1.20 2006/01/17 19:44:39 hlellelid Exp $
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
 * MySQL implementation of TableInfo.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.20 $
 * @package   creole.drivers.mysql.metadata
 */
class MySQLTableInfo extends TableInfo {

    /** Loads the columns for this table. */
    protected function initColumns()
    {
        include_once 'creole/metadata/ColumnInfo.php';
        include_once 'creole/drivers/mysql/MySQLTypes.php';

        if (!@mysql_select_db($this->dbname, $this->conn->getResource())) {
            throw new SQLException('No database selected');
        }

        // To get all of the attributes we need, we use
        // the MySQL "SHOW COLUMNS FROM $tablename" SQL.  We cannot
        // use the API functions (e.g. mysql_list_fields() because they
        // do not return complete information -- e.g. precision / scale, default
        // values).

        $res = mysql_query("SHOW COLUMNS FROM `" . $this->name . "`", $this->conn->getResource());

        $defaults = array();
        $nativeTypes = array();
        $precisions = array();

        while($row = mysql_fetch_assoc($res)) {
            $name = $row['Field'];
            $is_nullable = ($row['Null'] == 'YES');
            $is_auto_increment = (strpos($row['Extra'], 'auto_increment') !== false);
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
            //BLOBs can't have any default values in MySQL
            $default = preg_match('~blob|text~', $nativeType) ? null : $row['Default'];
            $this->columns[$name] = new ColumnInfo($this,
                                                   $name,
                                                   MySQLTypes::getType($nativeType),
                                                   $nativeType,
                                                   $size,
                                                   $precision,
                                                   $scale,
                                                   $is_nullable,
                                                   $default,
                                                   $is_auto_increment,
                                                   $row);
        }

        $this->colsLoaded = true;
    }

    /** Loads the primary key information for this table. */
    protected function initPrimaryKey()
    {
        include_once 'creole/metadata/PrimaryKeyInfo.php';

        // columns have to be loaded first
        if (!$this->colsLoaded) $this->initColumns();

        if (!@mysql_select_db($this->dbname, $this->conn->getResource())) {
            throw new SQLException('No database selected');
        }

        // Primary Keys
        $res = mysql_query("SHOW KEYS FROM `" . $this->name . "`", $this->conn->getResource());

        // Loop through the returned results, grouping the same key_name together
        // adding each column for that key.

        while($row = mysql_fetch_assoc($res)) {
            // Skip any non-primary keys.
            if ($row['Key_name'] !== 'PRIMARY') {
                continue;
            }
            $name = $row["Column_name"];
            if (!isset($this->primaryKey)) {
                $this->primaryKey = new PrimaryKeyInfo($name, $row);
            }
            $this->primaryKey->addColumn($this->columns[$name]);
        }

        $this->pkLoaded = true;
    }

    /** Loads the indexes for this table. */
    protected function initIndexes() {

        include_once 'creole/metadata/IndexInfo.php';

        // columns have to be loaded first
        if (!$this->colsLoaded) $this->initColumns();

        if (!@mysql_select_db($this->dbname, $this->conn->getResource())) {
            throw new SQLException('No database selected');
        }

        // Indexes
        $res = mysql_query("SHOW INDEX FROM `" . $this->name . "`", $this->conn->getResource());

        // Loop through the returned results, grouping the same key_name together
        // adding each column for that key.

        while($row = mysql_fetch_assoc($res)) {
            $colName = $row["Column_name"];
            $name = $row["Key_name"];

            if($name == "PRIMARY") {
                continue;
            }

            if (!isset($this->indexes[$name])) {
                $isUnique = ($row["Non_unique"] == 0);
                $this->indexes[$name] = new IndexInfo($name, $isUnique, $row);
            }
            $this->indexes[$name]->addColumn($this->columns[$colName]);
        }

        $this->indexesLoaded = true;
    }

  /**
   * Load foreign keys for supporting versions of MySQL.
   * @author Tony Bibbs
   */
  protected function initForeignKeys() {

    // First make sure we have supported version of MySQL:
    $res = mysql_query("SELECT VERSION()");
    $row = mysql_fetch_row($res);

    // Yes, it is OK to hardcode this...this was the first version of MySQL
    // that supported foreign keys
    if ($row[0] < '3.23.44') {
       $this->fksLoaded = true;
       return;
    }

    include_once 'creole/metadata/ForeignKeyInfo.php';

    // columns have to be loaded first
    if (!$this->colsLoaded) $this->initColumns();
    if (!@mysql_select_db($this->dbname, $this->conn->getResource())) {
      throw new SQLException('No database selected');
    }
       // Get the CREATE TABLE syntax
    $res = mysql_query("SHOW CREATE TABLE `" . $this->name . "`", $this->conn->getResource());
    $row = mysql_fetch_row($res);

    // Get the information on all the foreign keys
    $regEx = '/FOREIGN KEY \(`([^`]*)`\) REFERENCES `([^`]*)` \(`([^`]*)`\)(.*)/';
    if (preg_match_all($regEx,$row[1],$matches)) {
      $tmpArray = array_keys($matches[0]);
      foreach ($tmpArray as $curKey) {
        $name = $matches[1][$curKey];
        $ftbl = $matches[2][$curKey];
        $fcol = $matches[3][$curKey];
        $fkey = $matches[4][$curKey];
        if (!isset($this->foreignKeys[$name])) {
          $this->foreignKeys[$name] = new ForeignKeyInfo($name);
          if ($this->database->hasTable($ftbl)) {
            $foreignTable = $this->database->getTable($ftbl);
          } else {
            $foreignTable = new MySQLTableInfo($this->database, $ftbl);
            $this->database->addTable($foreignTable);
          }
          if ($foreignTable->hasColumn($fcol)) {
            $foreignCol = $foreignTable->getColumn($fcol);
          } else {
            $foreignCol = new ColumnInfo($foreignTable, $fcol);
            $foreignTable->addColumn($foreignCol);
          }

          //typical for mysql is RESTRICT
          $fkactions = array(
            'ON DELETE'	=> ForeignKeyInfo::RESTRICT,
            'ON UPDATE'	=> ForeignKeyInfo::RESTRICT,
          );
                              
          if ($fkey) {
            //split foreign key information -> search for ON DELETE and afterwords for ON UPDATE action
            foreach (array_keys($fkactions) as $fkaction) {
              $result = NULL;
              preg_match('/' . $fkaction . ' (' . ForeignKeyInfo::CASCADE . '|' . ForeignKeyInfo::SETNULL . ')/', $fkey, $result);
              if ($result && is_array($result) && isset($result[1])) {
                $fkactions[$fkaction] = $result[1];
              }
            }
          }

          $this->foreignKeys[$name]->addReference($this->columns[$name], $foreignCol, $fkactions['ON DELETE'], $fkactions['ON UPDATE']);
        }
      }
    }
    $this->fksLoaded = true;
    
  }

  protected function initVendorSpecificInfo()
  {
      $res = mysql_query("SHOW TABLE STATUS LIKE '" . $this->name . "'", $this->conn->getResource());
      $this->vendorSpecificInfo = mysql_fetch_assoc($res);

      $this->vendorLoaded = true;
  }

}
