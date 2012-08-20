<?php
/*
 *  $Id: PgSQLIdGenerator.php,v 1.5 2004/03/19 14:19:50 hlellelid Exp $
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
 
require_once 'creole/IdGenerator.php';

/**
 * PostgreSQL IdGenerator implemenation.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.5 $
 * @package   creole.drivers.pgsql
 */
class PgSQLIdGenerator implements IdGenerator {
    
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
        $rs = $this->conn->executeQuery("SELECT nextval('" . pg_escape_string ( $name ) . "')", ResultSet::FETCHMODE_NUM);
        $rs->next();
        return $rs->getInt(1);
    }
    
}

