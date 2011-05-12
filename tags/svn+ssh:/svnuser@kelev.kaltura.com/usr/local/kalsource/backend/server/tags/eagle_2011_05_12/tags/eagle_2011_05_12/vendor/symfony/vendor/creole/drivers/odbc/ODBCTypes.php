<?php

/*
 *  $Id: ODBCTypes.php,v 1.1 2004/07/27 23:08:30 hlellelid Exp $
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

require_once 'creole/CreoleTypes.php';

/**
 * ODBC types / type map.
 *
 * @author    Dave Lawson <dlawson@masterytech.com>
 * @version   $Revision: 1.1 $
 * @package   creole.drivers.odbc
 */
class ODBCTypes extends CreoleTypes {

    /**
     * Map ODBC native types to Creole (JDBC) types.
     */
    protected static $typeMap = null;

    /**
     * Reverse mapping, created on demand.
     */
    protected static $reverseMap = null;

    /**
     * Loads the map of ODBC data types to Creole (JDBC) types.
     *
     * NOTE: This function cannot map DBMS-specific datatypes. If you use a
     *       driver which implements DBMS-specific datatypes, you will need
     *       to modify/extend this class to add the correct mapping.
     */
    public static function loadTypeMap($conn = null)
    {
        if (self::$typeMap !== null && count(self::$typeMap) > 0)
            return;

        if ($conn == null)
            throw new SQLException('No connection specified when loading ODBC type map.');

        self::$typeMap = array();

        $result = @odbc_gettypeinfo($conn->getResource());

        if ($result === false)
            throw new SQLException('Failed to retrieve type info.', $conn->nativeError());

        $rowNum = 1;

        while (odbc_fetch_row($result, $rowNum++))
        {
            $odbctypeid = odbc_result($result, 'DATA_TYPE');
            $odbctypename = odbc_result($result, 'TYPE_NAME');

            switch ($odbctypeid)
            {
                case SQL_CHAR:
                    self::$typeMap[$odbctypename] = CreoleTypes::CHAR;
                    break;
                case SQL_VARCHAR:
                    self::$typeMap[$odbctypename] = CreoleTypes::VARCHAR;
                    break;
                case SQL_LONGVARCHAR:
                    self::$typeMap[$odbctypename] = CreoleTypes::LONGVARCHAR;
                    break;
                case SQL_DECIMAL:
                    self::$typeMap[$odbctypename] = CreoleTypes::DECIMAL;
                    break;
                case SQL_NUMERIC:
                    self::$typeMap[$odbctypename] = CreoleTypes::NUMERIC;
                    break;
                case SQL_BIT:
                    self::$typeMap[$odbctypename] = CreoleTypes::BOOLEAN;
                    break;
                case SQL_TINYINT:
                    self::$typeMap[$odbctypename] = CreoleTypes::TINYINT;
                    break;
                case SQL_SMALLINT:
                    self::$typeMap[$odbctypename] = CreoleTypes::SMALLINT;
                    break;
                case SQL_INTEGER:
                    self::$typeMap[$odbctypename] = CreoleTypes::INTEGER;
                    break;
                case SQL_BIGINT:
                    self::$typeMap[$odbctypename] = CreoleTypes::BIGINT;
                    break;
                case SQL_REAL:
                    self::$typeMap[$odbctypename] = CreoleTypes::REAL;
                    break;
                case SQL_FLOAT:
                    self::$typeMap[$odbctypename] = CreoleTypes::FLOAT;
                    break;
                case SQL_DOUBLE:
                    self::$typeMap[$odbctypename] = CreoleTypes::DOUBLE;
                    break;
                case SQL_BINARY:
                    self::$typeMap[$odbctypename] = CreoleTypes::BINARY;
                    break;
                case SQL_VARBINARY:
                    self::$typeMap[$odbctypename] = CreoleTypes::VARBINARY;
                    break;
                case SQL_LONGVARBINARY:
                    self::$typeMap[$odbctypename] = CreoleTypes::LONGVARBINARY;
                    break;
                case SQL_DATE:
                    self::$typeMap[$odbctypename] = CreoleTypes::DATE;
                    break;
                case SQL_TIME:
                    self::$typeMap[$odbctypename] = CreoleTypes::TIME;
                    break;
                case SQL_TIMESTAMP:
                    self::$typeMap[$odbctypename] = CreoleTypes::TIMESTAMP;
                    break;
                case SQL_TYPE_DATE:
                    self::$typeMap[$odbctypename] = CreoleTypes::DATE;
                    break;
                case SQL_TYPE_TIME:
                    self::$typeMap[$odbctypename] = CreoleTypes::TIME;
                    break;
                case SQL_TYPE_TIMESTAMP:
                    self::$typeMap[$odbctypename] = CreoleTypes::TIMESTAMP;
                    break;
                default:
                    self::$typeMap[$odbctypename] = CreoleTypes::OTHER;
                    break;
            }
        }

        @odbc_free_result($result);
    }

    /**
     * This method returns the generic Creole (JDBC-like) type
     * when given the native db type.
     * @param string $nativeType DB native type (e.g. 'TEXT', 'byetea', etc.).
     * @return int Creole native type (e.g. CreoleTypes::LONGVARCHAR, CreoleTypes::BINARY, etc.).
     */
    public static function getType($nativeType)
    {
        if (!self::$typeMap)
            self::loadTypeMap();

        $t = strtoupper($nativeType);

        if (isset(self::$typeMap[$t])) {
            return self::$typeMap[$t];
        } else {
            return CreoleTypes::OTHER;
        }
    }

    /**
     * This method will return a native type that corresponds to the specified
     * Creole (JDBC-like) type.
     * If there is more than one matching native type, then the LAST defined
     * native type will be returned.
     * @param int $creoleType
     * @return string Native type string.
     */
    public static function getNativeType($creoleType)
    {
        if (!self::$typeMap)
            self::loadTypeMap();

        if (self::$reverseMap === null) {
            self::$reverseMap = array_flip(self::$typeMap);
        }
        return @self::$reverseMap[$creoleType];
    }

}