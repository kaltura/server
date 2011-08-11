<?php

require_once 'creole/IdGenerator.php';

/**
 * MySQL IdGenerator implimenation.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.6 $
 * @package   creole.drivers.mysql
 */
class MySQLIdGenerator implements IdGenerator {
    
    /** Connection object that instantiated this class */
    private $conn;

    /**
     * Creates a new IdGenerator class, saves passed connection for use
     * later by getId() method.
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }
    
    /**
     * @see IdGenerator::isBeforeInsert()
     */
    public function isBeforeInsert()
    {
        return false;
    }    
    
    /**
     * @see IdGenerator::isAfterInsert()
     */
    public function isAfterInsert()
    {
        return true;
    }
        
    /**
     * @see IdGenerator::getIdMethod()
     */
    public function getIdMethod()
    {
        return self::AUTOINCREMENT;
    }
    
    /**
     * Returns last-generated auto-increment ID.
     * 
     * Note that for very large values (2,147,483,648 to 9,223,372,036,854,775,807) a string
     * will be returned, because these numbers are larger than supported by PHP's native
     * numeric datatypes.
     * 
     * @see IdGenerator::getId()
     */
    public function getId($unused = null)
    {
        $insert_id = mysql_insert_id($this->conn->getResource());
        if ( $insert_id < 0 ) {
            $insert_id = null;
            $result = mysql_query('SELECT LAST_INSERT_ID()', $this->conn->getResource());
            if ( $result ) {
                $row = mysql_fetch_row($result);
                $insert_id = $row ? $row[0] : null;
            }
        }
        return $insert_id;
    }
    
}

