<?php

/**
 * Interface for classes that provide functionality to get SEQUENCE or AUTO-INCREMENT ids from the database.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.3 $
 * @package   creole
 */
interface IdGenerator {
    
    /** SEQUENCE id generator type */
    const SEQUENCE = 1;
    
    /** AUTO INCREMENT id generator type */
    const AUTOINCREMENT = 2;                
    
    /**
     * Convenience method that returns TRUE if id is generated
     * before an INSERT statement.  This is the same as checking
     * whether the generator type is SEQUENCE.
     * @return boolean TRUE if gen id method is SEQUENCE
     * @see getIdMethod()
     */
    public function isBeforeInsert();
    
    /**
     * Convenience method that returns TRUE if id is generated
     * after an INSERT statement.  This is the same as checking
     * whether the generator type is AUTOINCREMENT.
     * @return boolean TRUE if gen id method is AUTOINCREMENT
     * @see getIdMethod()
     */
    public function isAfterInsert();
    
    /**
     * Get the preferred type / style for generating ids for RDBMS.
     * @return int SEQUENCE or AUTOINCREMENT
     */
    public function getIdMethod();
    
    /**
     * Get the autoincrement or sequence id given the current connection
     * and any additional needed info (e.g. sequence name for sequences).
     * <p>
     * Note: if you take advantage of the fact that $keyInfo may not be specified
     * you should make sure that your code is setup in such a way that it will
     * be portable if you change from an RDBMS that uses AUTOINCREMENT to one that
     * uses SEQUENCE (i.e. in which case you would need to specify sequence name).
     * 
     * @param mixed $keyInfo Any additional information (e.g. sequence name) needed to fetch the id.
     * @return int The last id / next id.
     */
    public function getId($keyInfo = null);
    
}

