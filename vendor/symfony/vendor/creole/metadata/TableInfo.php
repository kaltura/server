<?php

/*
 *  $Id: TableInfo.php,v 1.16 2005/10/17 19:05:10 dlawson_mi Exp $
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
 * Represents a table.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.16 $
 * @package   creole.metadata
 */
abstract class TableInfo {

    protected $name;
    protected $columns = array();
    protected $foreignKeys = array();
    protected $indexes = array();
    protected $primaryKey;

    protected $pkLoaded = false;
    protected $fksLoaded = false;
    protected $indexesLoaded = false;
    protected $colsLoaded = false;
    protected $vendorLoaded = false;

    /**
     * Additional and optional vendor specific information.
     * @var vendorSpecificInfo
     */
    protected $vendorSpecificInfo = array();

    /**
     * Database Connection.
     * @var Connection
     */
    protected $conn;

    /**
     * The parent DatabaseInfo object.
     * @var DatabaseInfo
     */
    protected $database;

    /** Shortcut to db resource link id (needed by drivers for queries). */
    protected $dblink;

    /** Shortcut to db name (needed by many drivers for queries). */
    protected $dbname;

    /**
     * @param string $table The table name.
     * @param string $database The database name.
     * @param resource $dblink The db connection resource.
     */
    function __construct(DatabaseInfo $database, $name) {
        $this->database = $database;
        $this->name = $name;
        $this->conn = $database->getConnection(); // shortcut because all drivers need this for the queries
        $this->dblink = $this->conn->getResource();
        $this->dbname = $database->getName();
    }

    /**
     * This "magic" method is invoked upon serialize().
     * Because the Info class hierarchy is recursive, we must handle
     * the serialization and unserialization of this object.
     * @return array The class variables that should be serialized (all must be public!).
     */
    function __sleep()
    {
        return array('name', 'columns', 'foreignKeys', 'indexes', 'primaryKey');
    }

    /**
     * This "magic" method is invoked upon unserialize().
     * This method re-hydrates the object and restores the recursive hierarchy.
     */
    function __wakeup()
    {
        // restore chaining
        foreach($this->columns as $col) {
            $col->table = $this;
        }
    }

    /**
     * Loads the columns.
     * @return void
     */
    abstract protected function initColumns();

    /**
     * Loads the primary key information for this table.
     * @return void
     */
    abstract protected function initPrimaryKey();

    /**
     * Loads the foreign keys for this table.
     * @return void
     */
    abstract protected function initForeignKeys();

    /**
     * Loads the indexes information for this table.
     * @return void
     */
    abstract protected function initIndexes();

    /**
     * Loads the vendor specific information for this table.
     * @return void
     */
    //it must be asbtract and be implemented in every vendor specific driver,
    //however since it's an experimental stuff it has an empty body in order
    //not to break BC
    /*abstract*/ protected function initVendorSpecificInfo(){}

    /**
     * Get parimary key in this table.
     * @throws Exception - if foreign keys are unsupported by DB.
     * @return array ForeignKeyInfo[]
     */
    public function getPrimaryKey()
    {
        if(!$this->pkLoaded) $this->initPrimaryKey();
        return $this->primaryKey;
    }

    /**
     * Get the ColumnInfo object for specified column.
     * @param string $name The column name.
     * @return ColumnInfo
     * @throws SQLException - if column does not exist for this table.
     */
    public function getColumn($name)
    {
        if(!$this->colsLoaded) $this->initColumns();
        if (!isset($this->columns[$name])) {
            throw new SQLException("Table `".$this->name."` has no column `".$name."`");
        }
        return $this->columns[$name];
    }

    /**
     * Return whether table contains specified column.
     * @param string $name The column name.
     * @return boolean
     */
    public function hasColumn($name)
    {
        if(!$this->colsLoaded) $this->initColumns();
        return isset($this->columns[$name]);
    }

    /**
     * Get array of columns for this table.
     * @return array ColumnInfo[]
     */
    public function getColumns()
    {
        if(!$this->colsLoaded) $this->initColumns();
        return array_values($this->columns); // re-key numerically
    }

    /**
     * Get specified fk for this table.
     * @param string $name The foreign key name to retrieve.
     * @return ForeignKeyInfo
     * @throws SQLException - if fkey does not exist for this table.
     */
    public function getForeignKey($name)
    {
        if(!$this->fksLoaded) $this->initForeignKeys();
        if (!isset($this->foreignKeys[$name])) {
            throw new SQLException("Table `".$this->name."` has no foreign key `".$name."`");
        }
        return $this->foreignKeys[$name];
    }

    /**
     * Get all foreign keys.
     * @return array ForeignKeyInfo[]
     */
    public function getForeignKeys()
    {
        if(!$this->fksLoaded) $this->initForeignKeys();
        return array_values($this->foreignKeys);
    }

    /**
     * Gets the IndexInfo object for a specified index.
     * @param string $name The index name to retrieve.
     * @return IndexInfo
     * @throws SQLException - if index does not exist for this table.
     */
    public function getIndex($name)
    {
        if(!$this->indexesLoaded) $this->initIndexes();
        if (!isset($this->indexes[$name])) {
            throw new SQLException("Table `".$this->name."` has no index `".$name."`");
        }
        return $this->indexes[$name];
    }

    /**
     * Get array of IndexInfo objects for this table.
     * @return array IndexInfo[]
     */
    public function getIndexes()
    {
        if(!$this->indexesLoaded) $this->initIndexes();
        return array_values($this->indexes);
    }

    /**
     * Alias for getIndexes() method.
     * @return array
     */
    public function getIndices()
    {
        return $this->getIndexes();
    }

    /**
     * Get table name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->name;
    }

    /** Have foreign keys been loaded? */
    public function foreignKeysLoaded()
    {
        return $this->fksLoaded;
    }

    /** Has primary key info been loaded? */
    public function primaryKeyLoaded()
    {
        return $this->pkLoaded;
    }

    /** Have columns been loaded? */
    public function columnsLoaded()
    {
        return $this->colsLoaded;
    }

    /** Has index information been loaded? */
    public function indexesLoaded()
    {
        return $this->indexesLoaded;
    }

    /**
     * Get vendor specific optional information for this table.
     * @return array vendorSpecificInfo[]
     */
    public function getVendorSpecificInfo()
    {
        if(!$this->vendorLoaded) $this->initVendorSpecificInfo();
        return $this->vendorSpecificInfo;
    }

    /** Adds a column to this table. */
    public function addColumn(ColumnInfo $column)
    {
        $this->columns[$column->getName()] = $column;
    }

    /** Get the parent DatabaseInfo object. */
    public function getDatabase()
    {
        return $this->database;
    }
}
