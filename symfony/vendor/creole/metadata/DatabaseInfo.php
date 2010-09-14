<?php

/*
 *  $Id: DatabaseInfo.php,v 1.15 2005/11/08 04:24:50 hlellelid Exp $
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
 * "Info" metadata class for a database.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.15 $
 * @package   creole.metadata
 */
abstract class DatabaseInfo {

    protected $tables = array();

    protected $sequences = array();

    /** have tables been loaded */
    protected $tablesLoaded = false;

    /** have sequences been loaded */
    protected $seqsLoaded = false;

    /** additional vendor specific information */
    private $vendorSpecificInfo = array();

    /**
     * The database Connection.
     * @var Connection
     */
    protected $conn;

    /** Database name. */
    protected $dbname;

    /**
     * Database link
     * @var resource
     */
    protected $dblink;

    /**
     * @param Connection $dbh
     */
    public function __construct(Connection $conn, $vendorInfo = array())
    {
        $this->conn = $conn;
        $this->dblink = $conn->getResource();
        $dsn = $conn->getDSN();
        $this->dbname = $dsn['database'];
        $this->vendorSpecificInfo = $vendorInfo;
    }

    /**
     * Get name of database.
     * @return string
     */
    public function getName()
    {
        return $this->dbname;
    }

    /**
     * This method is invoked upon serialize().
     * Because the Info class hierarchy is recursive, we must handle
     * the serialization and unserialization of this object.
     * @return array The class variables that should be serialized (all must be public!).
     */
    function __sleep()
    {
        return array('tables','sequences','conn');
    }

    /**
     * This method is invoked upon unserialize().
     * This method re-hydrates the object and restores the recursive hierarchy.
     */
    function __wakeup()
    {
        // Re-init vars from serialized connection
        $this->dbname = $conn->database;
        $this->dblink = $conn->connection;

        // restore chaining
        foreach($this->tables as $tbl) {
            $tbl->database = $this;
            $tbl->dbname = $this->dbname;
            $tbl->dblink = $this->dblink;
            $tbl->schema = $this->schema;
        }
    }

    /**
     * Returns Connection being used.
     * @return Connection
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Get the TableInfo object for specified table name.
     * @param string $name The name of the table to retrieve.
     * @return TableInfo
     * @throws SQLException - if table does not exist in this db.
     */
    public function getTable($name)
    {
        if(!$this->tablesLoaded) $this->initTables();
        if (!isset($this->tables[strtoupper($name)])) {
            throw new SQLException("Database `".$this->dbname."` has no table `".$name."`");
        }
        return $this->tables[ strtoupper($name) ];
    }

  /**
   * Return whether database contains specified table.
   * @param string $name The table name.
   * @return boolean
   */
  public function hasTable($name)
  {
    if(!$this->tablesLoaded) $this->initTables();
    return isset($this->tables[strtoupper($name)]);
  }

    /**
     * Gets array of TableInfo objects.
     * @return array TableInfo[]
     */
    public function getTables()
    {
        if(!$this->tablesLoaded) $this->initTables();
        return array_values($this->tables); //re-key [numerically]
    }

    /**
     * Adds a table to this db.
     * Table name is case-insensitive.
     * @param TableInfo $table
     */
    public function addTable(TableInfo $table)
    {
        $this->tables[strtoupper($table->getName())] = $table;
    }

    /**
     * @return void
     * @throws SQLException
     */
    abstract protected function initTables();

    /**
     * @return void
     * @throws SQLException
     */
    abstract protected function initSequences();

    /**
     * @return boolean
     * @throws SQLException
     */
    public function isSequence($key)
    {
        if(!$this->seqsLoaded) $this->initSequences();
        return isset($this->sequences[ strtoupper($key) ]);
    }

    /**
     * Gets array of ? objects.
     * @return array ?[]
     */
    public function getSequences()
    {
        if(!$this->seqsLoaded) $this->initSequences();
        return array_values($this->sequences); //re-key [numerically]
    }

    /**
     * Get vendor specific optional information for this primary key.
     * @return array vendorSpecificInfo[]
     */
    public function getVendorSpecificInfo()
    {
        return $this->vendorSpecificInfo;
    }
}

