<?php

/*
 *  $Id: MSSQLTypes.php,v 1.8 2004/07/27 23:16:50 hlellelid Exp $
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
 * MSSQL types / type map.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.8 $
 * @package   creole.drivers.mssql
 */
class MSSQLTypes extends CreoleTypes {

    /** Map PostgreSQL native types to Creole (JDBC) types. */
    private static $typeMap = array (
                "binary" => CreoleTypes::BINARY,
                "bit" => CreoleTypes::BOOLEAN, 
                "char" => CreoleTypes::CHAR,
                "datetime" => CreoleTypes::TIMESTAMP,
                "decimal() identity"  => CreoleTypes::DECIMAL,
                "decimal"  => CreoleTypes::DECIMAL,                
                "image" => CreoleTypes::LONGVARBINARY,
                "int" => CreoleTypes::INTEGER,
                "int identity" => CreoleTypes::INTEGER,
                "integer" => CreoleTypes::INTEGER,
                "money" => CreoleTypes::DECIMAL, 
                "nchar" => CreoleTypes::CHAR, 
                "ntext" => CreoleTypes::LONGVARCHAR, 
                "numeric() identity" => CreoleTypes::NUMERIC,
                "numeric" => CreoleTypes::NUMERIC,                  
                "nvarchar" => CreoleTypes::VARCHAR,
                "real" => CreoleTypes::REAL, 
                "float" => CreoleTypes::FLOAT,
                "smalldatetime" => CreoleTypes::TIMESTAMP, 
                "smallint" => CreoleTypes::SMALLINT, 
                "smallint identity" => CreoleTypes::SMALLINT,
                "smallmoney" => CreoleTypes::DECIMAL,
                "sysname" => CreoleTypes::VARCHAR,
                "text" => CreoleTypes::LONGVARCHAR,
                "timestamp" => CreoleTypes::BINARY,
                "tinyint identity" => CreoleTypes::TINYINT, 
                "tinyint" => CreoleTypes::TINYINT,                 
                "uniqueidentifier" => CreoleTypes::CHAR,
                "varbinary" => CreoleTypes::VARBINARY,
                "varchar" => CreoleTypes::VARCHAR,
                "uniqueidentifier" => CreoleTypes::CHAR,
                // SQL Server 2000 only
                "bigint identity" => CreoleTypes::BIGINT,
                "bigint" => CreoleTypes::BIGINT,                
                "sql_variant" => CreoleTypes::VARCHAR,
                ); 
                 
    /** Reverse lookup map, created on demand. */
    private static $reverseMap = null;
    
    public static function getType($mssqlType)
    {    
        $t = strtolower($mssqlType);
        if (isset(self::$typeMap[$t])) {
            return self::$typeMap[$t];
        } else {
            return CreoleTypes::OTHER;
        }
    }
    
    public static function getNativeType($creoleType)
    {
        if (self::$reverseMap === null) {
            self::$reverseMap = array_flip(self::$typeMap);
        }
        return @self::$reverseMap[$creoleType];
    }
    
}