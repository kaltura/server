<?php
/*
 *  $Id: SQLiteTableInfo.php,v 1.8 2005/10/18 02:27:50 hlellelid Exp $
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
 * @version   $Revision: 1.8 $
 * @package   creole.drivers.sqlite.metadata
 */
class SQLiteTableInfo extends TableInfo {
    
    /** Loads the columns for this table. */
    protected function initColumns() 
    {
        
        include_once 'creole/metadata/ColumnInfo.php';
        include_once 'creole/metadata/PrimaryKeyInfo.php';
        include_once 'creole/drivers/sqlite/SQLiteTypes.php';                
        
        // To get all of the attributes we need, we'll actually do 
        // two separate queries.  The first gets names and default values
        // the second will fill in some more details.
        
        $sql = "PRAGMA table_info('".$this->name."')";
                
        $res = sqlite_query($this->conn->getResource(), $sql);
        
        
        while($row = sqlite_fetch_array($res, SQLITE_ASSOC)) {
        
            $name = $row['name'];
            
            $fulltype = $row['type'];            
            $size = null;
            $precision = null;
            $scale = null;
            
            if (preg_match('/^([^\(]+)\(\s*(\d+)\s*,\s*(\d+)\s*\)$/', $fulltype, $matches)) {
                $type = $matches[1];
                $precision = $matches[2];
                $scale = $matches[3]; // aka precision    
            } elseif (preg_match('/^([^\(]+)\(\s*(\d+)\s*\)$/', $fulltype, $matches)) {
                $type = $matches[1];
                $size = $matches[2];
            } else {
                $type = $fulltype;
            }
            // If column is primary key and of type INTEGER, it is auto increment
            // See: http://sqlite.org/faq.html#q1
            $is_auto_increment = ($row['pk'] == 1 && $fulltype == 'INTEGER');
            $not_null = $row['notnull'];
            $is_nullable = !$not_null;
            
            $default_val = $row['dflt_value'];
            
            $this->columns[$name] = new ColumnInfo($this, $name, SQLiteTypes::getType($type), $type, $size, $precision, $scale, $is_nullable, $default_val);
            
            if (($row['pk'] == 1) || (strtolower($type) == 'integer primary key')) {
                if ($this->primaryKey === null) {
                    $this->primaryKey = new PrimaryKeyInfo($name);
                }
                $this->primaryKey->addColumn($this->columns[ $name ]);
            }
            
        }        
                
        $this->colsLoaded = true;
    }
    
    /** Loads the primary key information for this table. */
    protected function initPrimaryKey()
    {        
        // columns have to be loaded first
        if (!$this->colsLoaded) $this->initColumns();                        
        // keys are loaded by initColumns() in this class.
        $this->pkLoaded = true;
    }
    
    /** Loads the indexes for this table. */
    protected function initIndexes() {
    
        include_once 'creole/metadata/IndexInfo.php';        

        // columns have to be loaded first
        if (!$this->colsLoaded) $this->initColumns();        

        $sql = "PRAGMA index_list('".$this->name."')";
        $res = sqlite_query($this->conn->getResource(), $sql);
        
        while($row = sqlite_fetch_array($res, SQLITE_ASSOC)) {        
            $name = $row['name'];
            $this->indexes[$name] = new IndexInfo($name);
            
            // get columns for that index
            $res2 = sqlite_query($this->conn->getResource(), "PRAGMA index_info('$name')");
            while($row2 = sqlite_fetch_array($res2, SQLITE_ASSOC)) {
                $colname = $row2['name'];
                $this->indexes[$name]->addColumn($this->columns[ $colname ]);
            }
        }        
                
        $this->indexesLoaded = true;
    }
    
    /** Load foreign keys (unsupported in SQLite). */
    protected function initForeignKeys() {
        
        // columns have to be loaded first
        if (!$this->colsLoaded) $this->initColumns();        
        
        // No fkeys in SQLite
        
        $this->fksLoaded = true;
    }
    
}
