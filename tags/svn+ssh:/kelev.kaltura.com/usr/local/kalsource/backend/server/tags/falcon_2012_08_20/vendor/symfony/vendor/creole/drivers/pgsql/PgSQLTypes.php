<?php

/*
 *  $Id: PgSQLTypes.php,v 1.8 2004/04/09 19:16:05 hlellelid Exp $
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
 * PostgreSQL types / type map.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.8 $
 * @package   creole.drivers.pgsql
 */
class PgSQLTypes extends CreoleTypes {

    /** Map PostgreSQL native types to Creole (JDBC) types. */
    private static $typeMap = array (
                "int2" => CreoleTypes::SMALLINT,
                "int4" => CreoleTypes::INTEGER, 
                "oid" => CreoleTypes::INTEGER,
                "int8" => CreoleTypes::BIGINT,
                "cash"  => CreoleTypes::DOUBLE,
                "money"  => CreoleTypes::DOUBLE,
                "numeric" => CreoleTypes::NUMERIC,
                "float4" => CreoleTypes::REAL,
                "float8" => CreoleTypes::DOUBLE,
                "bpchar" => CreoleTypes::CHAR, 
                "char" => CreoleTypes::CHAR, 
                "char2" => CreoleTypes::CHAR, 
                "char4" => CreoleTypes::CHAR, 
                "char8" => CreoleTypes::CHAR, 
                "char16" => CreoleTypes::CHAR,
                "varchar" => CreoleTypes::VARCHAR, 
                "text" => CreoleTypes::VARCHAR, 
                "name" => CreoleTypes::VARCHAR, 
                "filename" => CreoleTypes::VARCHAR,
                "bytea" => CreoleTypes::BINARY,
                "bool" => CreoleTypes::BOOLEAN,
                "date" => CreoleTypes::DATE,
                "time" => CreoleTypes::TIME,
                "abstime" => CreoleTypes::TIMESTAMP, 
                "timestamp" => CreoleTypes::TIMESTAMP, 
                "timestamptz" => CreoleTypes::TIMESTAMP,
                "_bool" => CreoleTypes::ARR,     
                "_char" => CreoleTypes::ARR, 
                "_int2" => CreoleTypes::ARR, 
                "_int4" => CreoleTypes::ARR, 
                "_text" => CreoleTypes::ARR,
                "_oid" => CreoleTypes::ARR, 
                "_varchar" => CreoleTypes::ARR, 
                "_int8" => CreoleTypes::ARR, 
                "_float4" => CreoleTypes::ARR, 
                "_float8" => CreoleTypes::ARR,
                "_abstime" => CreoleTypes::ARR, 
                "_date" => CreoleTypes::ARR, 
                "_time" => CreoleTypes::ARR, 
                "_timestamp" => CreoleTypes::ARR, 
                "_numeric" => CreoleTypes::ARR,
                "_bytea" => CreoleTypes::ARR,
            );
                
    /** Reverse lookup map, created on demand. */
    private static $reverseMap = null;
    
    public static function getType($pgsqlType)
    {
        $t = strtolower($pgsqlType);
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