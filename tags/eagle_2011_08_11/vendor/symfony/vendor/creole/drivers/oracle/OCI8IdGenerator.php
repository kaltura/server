<?php

require_once 'creole/IdGenerator.php';

/**
 * Oracle (OCI8) IdGenerator implimenation.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.5 $
 * @package   creole.drivers.oracle
 */
class OCI8IdGenerator implements IdGenerator {
    
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
        return true;
    }    
    
    /**
     * @see IdGenerator::isAfterInsert()
     */
    public function isAfterInsert()
    {
        return false;
    }
        
    /**
     * @see IdGenerator::getIdMethod()
     */
    public function getIdMethod()
    {
        return self::SEQUENCE;
    }
    
    /**
     * @see IdGenerator::getId()
     */
    public function getId($name = null)
    {
        if ($name === null) {
            throw new SQLException("You must specify the sequence name when calling getId() method.");
        }
        $rs = $this->conn->executeQuery("select " . $name . ".nextval from dual", ResultSet::FETCHMODE_NUM);
        $rs->next();
        return $rs->getInt(1);
    }
    
}

