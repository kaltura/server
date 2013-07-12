<?php
/*
 * $Id: MySQLiIdGenerator.php,v 1.4 2004/09/18 09:15:49 sb Exp $
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
 * MySQLi implementation of IdGenerator.
 *
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @version   $Revision: 1.4 $
 * @package   creole.drivers.mysqli
 */
class MySQLiIdGenerator implements IdGenerator {
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
        $resource = $this->conn->getResource();
        $insert_id = mysqli_insert_id($resource);

        if ( $insert_id < 0 ) {
            $insert_id = null;

            $result = mysqli_query($resource, 'SELECT LAST_INSERT_ID()');

            if ( $result ) {
                $row = mysqli_fetch_row($result);
                $insert_id = $row ? $row[0] : null;
            }
        }

        return $insert_id;
    }
}
