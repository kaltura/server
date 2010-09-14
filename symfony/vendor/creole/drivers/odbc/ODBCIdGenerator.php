<?php

require_once 'creole/IdGenerator.php';

/**
 * ODBC IdGenerator implimenation.
 *
 * NOTE: I tried keeping the SQL as basic as possible in this class.
 *       If you need something more optimized, derive your own IdGenerator
 *       and use {@link ODBCAdapter::getIdGenerator()} to use it.
 *
 * @author    Dave Lawson <dlawson@masterytech.com>
 * @version   $Revision: 1.2 $
 * @package   creole.drivers.odbc
 */
class ODBCIdGenerator implements IdGenerator {

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
    public function getId($seqname = null)
    {
        if ($seqname === null)
            throw new SQLException('You must specify the sequence name when calling getId() method.');

        $triedcreate = false;

        while (1)
        {
            try
            {
                $n = $this->conn->executeUpdate("UPDATE $seqname SET id = id + 1", ResultSet::FETCHMODE_NUM);

                if ($n == 0)
                    throw new SQLException('Failed to update IdGenerator id', $this->conn->nativeError());

                $rs = $this->conn->executeQuery("SELECT id FROM $seqname", ResultSet::FETCHMODE_NUM);
            }
            catch (SQLException $e)
            {
                //$odbcerr = odbc_error($this->conn->getResource());

                if ($triedcreate)// || ($odbcerr != 'S0000' && $odbcerr != 'S0002'))
                    throw $e;

                $this->drop($seqname, true);
                $this->create($seqname);
                $triedcreate = true;
                continue;
            }

            break;
        }

        $rs->first();

        return $rs->getInt(1);
    }

    /**
     * Creates the sequence emulation table.
     */
    public function create($seqname)
    {
        $this->conn->executeUpdate("CREATE TABLE $seqname ( id numeric(19,0) NOT NULL )");
        $this->conn->executeUpdate("INSERT INTO $seqname ( id ) VALUES ( 0 )");
    }

    /**
     * Drops the sequence emulation table.
     */
    public function drop($seqname, $ignoreerrs = false)
    {
        try {
            $this->conn->executeUpdate("DROP TABLE $seqname");
        } catch (Exception $e) {
            if (!$ignoreerrs) throw $e;
        }
    }

}