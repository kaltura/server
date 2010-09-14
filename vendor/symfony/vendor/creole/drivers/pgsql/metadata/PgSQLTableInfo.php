<?php
/*
 *  $Id: PgSQLTableInfo.php,v 1.31 2006/01/17 19:44:40 hlellelid Exp $
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
 * PgSQL implementation of TableInfo.
 *
 * See this Python code by David M. Cook for some good reference on Pgsql metadata
 * functions:
 * @link http://www.sandpyt.org/pipermail/sandpyt/2003-March/000008.html
 *
 * Here's some more information from postgresql:
 * @link http://developer.postgresql.org/docs/pgsql/src/backend/catalog/information_schema.sql
 *
 * @todo -c Eventually move to supporting only Postgres >= 7.4, which has the information_schema
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.31 $
 * @package   creole.drivers.pgsql.metadata
 */
class PgSQLTableInfo extends TableInfo {
	
    /**
     * Database Version.
     * @var String
     */
    private $version;
	
    /**
     * Table OID
     * @var Integer
     */
    private $oid;

    /**
     * @param string $table The table name.
     * @param string $database The database name.
     * @param resource $dblink The db connection resource.
     */
    function __construct(DatabaseInfo $database, $name, $version, $intOID) {
        parent::__construct ($database, $name);
        $this->version = $version;
        $this->oid = $intOID;
    } // function __construct(DatabaseInfo $database, $name) {

    /** Load the columns for this table */
    protected function initColumns () {
    	// Include dependencies
    	include_once ('creole/metadata/ColumnInfo.php');
    	include_once ('creole/drivers/pgsql/PgSQLTypes.php');

    	// Get the columns, types, etc.
    	// Based on code from pgAdmin3 (http://www.pgadmin.org/)
    	$result = pg_query ($this->conn->getResource(), sprintf ("SELECT 
    								att.attname,
    								att.atttypmod,
    								att.atthasdef,
    								att.attnotnull,
    								def.adsrc, 
    								CASE WHEN att.attndims > 0 THEN 1 ELSE 0 END AS isarray, 
    								CASE 
    									WHEN ty.typname = 'bpchar' 
    										THEN 'char' 
    									WHEN ty.typname = '_bpchar' 
	    									THEN '_char' 
    									ELSE 
	    									ty.typname 
    								END AS typname,
    								ty.typtype
								FROM pg_attribute att
									JOIN pg_type ty ON ty.oid=att.atttypid
									LEFT OUTER JOIN pg_attrdef def ON adrelid=att.attrelid AND adnum=att.attnum
								WHERE att.attrelid = %d AND att.attnum > 0
									AND att.attisdropped IS FALSE
								ORDER BY att.attnum", $this->oid));

        if (!$result) {
            throw new SQLException("Could not list fields for table: " . $this->name, pg_last_error($this->conn->getResource()));
        }
        while($row = pg_fetch_assoc($result)) {
        	
        	$size = null;
        	$precision = null;
        	$scale = null;
        	
        	// Check to ensure that this column isn't an array data type
        	if (((int) $row['isarray']) === 1)
        	{
        		throw new SQLException (sprintf ("Array datatypes are not currently supported [%s.%s]", $this->name, $row['attname']));
        	} // if (((int) $row['isarray']) === 1)
            $name = $row['attname'];
            // If they type is a domain, Process it
            if (strtolower ($row['typtype']) == 'd')
            {
            	$arrDomain = $this->processDomain ($row['typname']);
            	$type = $arrDomain['type'];
            	$size = $arrDomain['length'];
            	$precision = $size;
            	$scale = $arrDomain['scale'];
            	$boolHasDefault = (strlen (trim ($row['atthasdef'])) > 0) ? $row['atthasdef'] : $arrDomain['hasdefault'];
            	$default = (strlen (trim ($row['adsrc'])) > 0) ? $row['adsrc'] : $arrDomain['default'];
            	$is_nullable = (strlen (trim ($row['attnotnull'])) > 0) ? $row['attnotnull'] : $arrDomain['notnull'];
            	$is_nullable = (($is_nullable == 't') ? false : true);
            } // if (strtolower ($row['typtype']) == 'd')
            else
            {
	            $type = $row['typname'];
	            $arrLengthPrecision = $this->processLengthScale ($row['atttypmod'], $type);
	            $size = $arrLengthPrecision['length'];
	            $precision = $size;
	            $scale = $arrLengthPrecision['scale'];
	            $boolHasDefault = $row['atthasdef'];
	            $default = $row['adsrc'];
	            $is_nullable = (($row['attnotnull'] == 't') ? false : true);
            } // else (strtolower ($row['typtype']) == 'd')

            $autoincrement = null;
                       
            // if column has a default
            if (($boolHasDefault == 't') && (strlen (trim ($default)) > 0))
            {
	            if (!preg_match('/^nextval\(/', $default))
	            {
	            	$strDefault= preg_replace ('/::[\W\D]*/', '', $default);
	            	$default = str_replace ("'", '', $strDefault);
	            } // if (!preg_match('/^nextval\(/', $row['atthasdef']))
	            else
	            {
	            	$autoincrement = true;
	            	$default = null;
	            } // else
            } // if (($boolHasDefault == 't') && (strlen (trim ($default)) > 0))
            else
            {
            	$default = null;
            } // else (($boolHasDefault == 't') && (strlen (trim ($default)) > 0))

            $this->columns[$name] = new ColumnInfo($this, $name, PgSQLTypes::getType($type), $type, $size, $precision, $scale, $is_nullable, $default, $autoincrement);
        }

        $this->colsLoaded = true;
    } // protected function initColumns ()

    private function processLengthScale ($intTypmod, $strName)
    {
    	// Define the return array
    	$arrRetVal = array ('length'=>null, 'scale'=>null);

    	// Some datatypes don't have a Typmod
    	if ($intTypmod == -1)
    	{
    		return $arrRetVal;
    	} // if ($intTypmod == -1)

    	// Numeric Datatype?
    	if ($strName == PgSQLTypes::getNativeType (CreoleTypes::NUMERIC))
    	{
    		$intLen = ($intTypmod - 4) >> 16;
    		$intPrec = ($intTypmod - 4) & 0xffff;
    		$intLen = sprintf ("%ld", $intLen);
    		if ($intPrec)
    		{
    			$intPrec = sprintf ("%ld", $intPrec);
    		} // if ($intPrec)
    		$arrRetVal['length'] = $intLen;
    		$arrRetVal['scale'] = $intPrec;
    	} // if ($strName == PgSQLTypes::getNativeType (CreoleTypes::NUMERIC))
    	elseif ($strName == PgSQLTypes::getNativeType (CreoleTypes::TIME) || $strName == 'timetz'
    		|| $strName == PgSQLTypes::getNativeType (CreoleTypes::TIMESTAMP) || $strName == 'timestamptz'
    		|| $strName == 'interval' || $strName == 'bit')
    	{
    		$arrRetVal['length'] = sprintf ("%ld", $intTypmod);
    	} // elseif (TIME, TIMESTAMP, INTERVAL, BIT)
    	else
    	{
    		$arrRetVal['length'] = sprintf ("%ld", ($intTypmod - 4));
    	} // else
    	return $arrRetVal;
    } // private function processLengthScale ($intTypmod, $strName)

    private function processDomain ($strDomain)
    {
    	if (strlen (trim ($strDomain)) < 1)
    	{
    		throw new SQLException ("Invalid domain name [" . $strDomain . "]");
    	} // if (strlen (trim ($strDomain)) < 1)
    	$result = pg_query ($this->conn->getResource(), sprintf ("SELECT
														d.typname as domname,
														b.typname as basetype,
														d.typlen,
														d.typtypmod,
														d.typnotnull,
														d.typdefault
													FROM pg_type d
														INNER JOIN pg_type b ON b.oid = CASE WHEN d.typndims > 0 then d.typelem ELSE d.typbasetype END
													WHERE
														d.typtype = 'd'
														AND d.typname = '%s'
													ORDER BY d.typname", $strDomain));

        if (!$result) {
            throw new SQLException("Query for domain [" . $strDomain . "] failed.", pg_last_error($this->conn->getResource()));
        }

        $row = pg_fetch_assoc ($result);
        if (!$row)
        {
        	throw new SQLException ("Domain [" . $strDomain . "] not found.");
        } // if (!$row)
        $arrDomain = array ();
        $arrDomain['type'] = $row['basetype'];
	    $arrLengthPrecision = $this->processLengthScale ($row['typtypmod'], $row['basetype']);
	    $arrDomain['length'] = $arrLengthPrecision['length'];
	    $arrDomain['scale'] = $arrLengthPrecision['scale'];
	    $arrDomain['notnull'] = $row['typnotnull'];
	    $arrDomain['default'] = $row['typdefault'];
	    $arrDomain['hasdefault'] = (strlen (trim ($row['typdefault'])) > 0) ? 't' : 'f';

	    pg_free_result ($result);
	    return $arrDomain;
    } // private function processDomain ($strDomain)

    /** Load foreign keys for this table. */
    protected function initForeignKeys()
    {
        include_once 'creole/metadata/ForeignKeyInfo.php';

        $result = pg_query ($this->conn->getResource(), sprintf ("SELECT
						      conname,
						      confupdtype,
						      confdeltype,
						      cl.relname as fktab,
						      a2.attname as fkcol,
						      cr.relname as reftab,
						      a1.attname as refcol
						FROM pg_constraint ct
						     JOIN pg_class cl ON cl.oid=conrelid
						     JOIN pg_class cr ON cr.oid=confrelid
						     LEFT JOIN pg_catalog.pg_attribute a1 ON a1.attrelid = ct.confrelid
						     LEFT JOIN pg_catalog.pg_attribute a2 ON a2.attrelid = ct.conrelid
						WHERE
						     contype='f'
						     AND conrelid = %d
						     AND a2.attnum = ct.conkey[1]
						     AND a1.attnum = ct.confkey[1]
						ORDER BY conname", $this->oid));
        if (!$result) {
            throw new SQLException("Could not list foreign keys for table: " . $this->name, pg_last_error($this->conn->getResource()));
        }

        while($row = pg_fetch_assoc($result)) {
            $name = $row['conname'];
            $local_table = $row['fktab'];
            $local_column = $row['fkcol'];
            $foreign_table = $row['reftab'];
            $foreign_column = $row['refcol'];

            // On Update
            switch ($row['confupdtype']) {
              case 'c':
                $onupdate = ForeignKeyInfo::CASCADE; break;
              case 'd':
                $onupdate = ForeignKeyInfo::SETDEFAULT; break;
              case 'n':
                $onupdate = ForeignKeyInfo::SETNULL; break;
              case 'r':
                $onupdate = ForeignKeyInfo::RESTRICT; break;
              default:
              case 'a':
                //NOACTION is the postgresql default
                $onupdate = ForeignKeyInfo::NONE; break;
            }
            // On Delete
            switch ($row['confdeltype']) {
              case 'c':
                $ondelete = ForeignKeyInfo::CASCADE; break;
              case 'd':
                $ondelete = ForeignKeyInfo::SETDEFAULT; break;
              case 'n':
                $ondelete = ForeignKeyInfo::SETNULL; break;
              case 'r':
                $ondelete = ForeignKeyInfo::RESTRICT; break;
              default:
              case 'a':
                //NOACTION is the postgresql default
                $ondelete = ForeignKeyInfo::NONE; break;
            }


            $foreignTable = $this->database->getTable($foreign_table);
            $foreignColumn = $foreignTable->getColumn($foreign_column);

            $localTable   = $this->database->getTable($local_table);
            $localColumn   = $localTable->getColumn($local_column);

            if (!isset($this->foreignKeys[$name])) {
                $this->foreignKeys[$name] = new ForeignKeyInfo($name);
            }
            $this->foreignKeys[$name]->addReference($localColumn, $foreignColumn, $ondelete, $onupdate);
        }

        $this->fksLoaded = true;
    }

    /** Load indexes for this table */
    protected function initIndexes()
    {
        include_once 'creole/metadata/IndexInfo.php';

        // columns have to be loaded first
        if (!$this->colsLoaded) $this->initColumns();

		$result = pg_query ($this->conn->getResource(), sprintf ("SELECT
													      DISTINCT ON(cls.relname)
													      cls.relname as idxname,
													      indkey,
													      indisunique
													FROM pg_index idx
													     JOIN pg_class cls ON cls.oid=indexrelid
													WHERE indrelid = %d AND NOT indisprimary
													ORDER BY cls.relname", $this->oid));


        if (!$result) {
            throw new SQLException("Could not list indexes keys for table: " . $this->name, pg_last_error($this->conn->getResource()));
        }

        while($row = pg_fetch_assoc($result)) {
            $name = $row["idxname"];
            $unique = ($row["indisunique"] == 't') ? true : false;
            if (!isset($this->indexes[$name])) {
                $this->indexes[$name] = new IndexInfo($name, $unique);
            }
            $arrColumns = explode (' ', $row['indkey']);
            foreach ($arrColumns as $intColNum)
            {
	            $result2 = pg_query ($this->conn->getResource(), sprintf ("SELECT a.attname
															FROM pg_catalog.pg_class c JOIN pg_catalog.pg_attribute a ON a.attrelid = c.oid
															WHERE c.oid = '%s' AND a.attnum = %d AND NOT a.attisdropped
															ORDER BY a.attnum", $this->oid, $intColNum));
				if (!$result2)
				{
            		throw new SQLException("Could not list indexes keys for table: " . $this->name, pg_last_error($this->conn->getResource()));
				}
				$row2 = pg_fetch_assoc($result2);
	            $this->indexes[$name]->addColumn($this->columns[ $row2['attname'] ]);
			} // foreach ($arrColumns as $intColNum)
        }

        $this->indexesLoaded = true;
    }

    /** Loads the primary keys for this table. */
    protected function initPrimaryKey() {

        include_once 'creole/metadata/PrimaryKeyInfo.php';


        // columns have to be loaded first
        if (!$this->colsLoaded) $this->initColumns();

        // Primary Keys
        
        $result = pg_query($this->conn->getResource(), sprintf ("SELECT
													      DISTINCT ON(cls.relname)
													      cls.relname as idxname,
													      indkey,
													      indisunique
													FROM pg_index idx
													     JOIN pg_class cls ON cls.oid=indexrelid
													WHERE indrelid = %s AND indisprimary
													ORDER BY cls.relname", $this->oid));
        if (!$result) {
            throw new SQLException("Could not list primary keys for table: " . $this->name, pg_last_error($this->conn->getResource()));
        }

        // Loop through the returned results, grouping the same key_name together
        // adding each column for that key.

        while($row = pg_fetch_assoc($result)) {
            $arrColumns = explode (' ', $row['indkey']);
            foreach ($arrColumns as $intColNum)
            {
	            $result2 = pg_query ($this->conn->getResource(), sprintf ("SELECT a.attname
															FROM pg_catalog.pg_class c JOIN pg_catalog.pg_attribute a ON a.attrelid = c.oid
															WHERE c.oid = '%s' AND a.attnum = %d AND NOT a.attisdropped
															ORDER BY a.attnum", $this->oid, $intColNum));
				if (!$result2)
				{
            		throw new SQLException("Could not list indexes keys for table: " . $this->name, pg_last_error($this->conn->getResource()));
				}
				$row2 = pg_fetch_assoc($result2);
				if (!isset($this->primaryKey)) {
					$this->primaryKey = new PrimaryKeyInfo($row2['attname']);
				}
	            $this->primaryKey->addColumn($this->columns[ $row2['attname'] ]);
			} // foreach ($arrColumns as $intColNum)
        }
        $this->pkLoaded = true;
    }

    

}