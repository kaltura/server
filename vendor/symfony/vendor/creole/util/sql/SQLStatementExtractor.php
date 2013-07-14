<?php
/*
 *  $Id: SQLStatementExtractor.php,v 1.5 2004/07/27 23:13:46 hlellelid Exp $
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
 
/**
 * Static class for extracting SQL statements from a string or file.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.5 $
 * @package   creole.util.sql
 */
class SQLStatementExtractor {
    
    protected static $delimiter = ';';
    
    /**
     * Get SQL statements from file.
     * 
     * @param string $filename Path to file to read.
     * @return array SQL statements
     */
    public static function extractFile($filename) {
        $buffer = file_get_contents($filename);
        if ($buffer === false) {
           throw new Exception("Unable to read file: " . $filename);
        }
        return self::extractStatements(self::getLines($buffer));
    }
    
    /**
     * Extract statements from string.
     * 
     * @param string $txt
     * @return array
     */
    public static function extract($buffer) {
        return self::extractStatements(self::getLines($buffer));
    }
    
    /**
     * Extract SQL statements from array of lines.
     *
     * @param array $lines Lines of the read-in file.
     * @return string
     */
    protected static function extractStatements($lines) {
        
        $statements = array();
        $sql = "";
               
        foreach($lines as $line) {
        
                $line = trim($line);
                
                if (self::startsWith("//", $line) || 
                    self::startsWith("--", $line) ||
                    self::startsWith("#", $line)) {
                    continue;
                }
                
                if (strlen($line) > 4 && strtoupper(substr($line,0, 4)) == "REM ") {
                    continue;
                }

                $sql .= " " . $line;
                $sql = trim($sql);

                // SQL defines "--" as a comment to EOL
                // and in Oracle it may contain a hint
                // so we cannot just remove it, instead we must end it
                if (strpos($line, "--") !== false) {
                    $sql .= "\n";
                }
    
                if (self::endsWith(self::$delimiter, $sql)) {
                    $statements[] = self::substring($sql, 0, strlen($sql)-1 - strlen(self::$delimiter));
                    $sql = "";
                }
            }
        return $statements;           
    }
    
    //
    // Some string helper methods
    // 
    
    /**
     * Tests if a string starts with a given string.
     * @param string $check The substring to check.
     * @param string $string The string to check in (haystack).
     * @return boolean True if $string starts with $check, or they are equal, or $check is empty.
     */
    protected static function startsWith($check, $string) {
        if ($check === "" || $check === $string) {
            return true;
        } else {
            return (strpos($string, $check) === 0) ? true : false;
        }
    }
    
    /**
     * Tests if a string ends with a given string.
     * @param string $check The substring to check.
     * @param string $string The string to check in (haystack).
     * @return boolean True if $string ends with $check, or they are equal, or $check is empty.
     */
    protected static function endsWith($check, $string) {
        if ($check === "" || $check === $string) {
            return true;
        } else {
            return (strpos(strrev($string), strrev($check)) === 0) ? true : false;
        }
    } 

    /**
     * a natural way of getting a subtring, php's circular string buffer and strange
     * return values suck if you want to program strict as of C or friends 
     */
    protected static function substring($string, $startpos, $endpos = -1) {
        $len    = strlen($string);
        $endpos = (int) (($endpos === -1) ? $len-1 : $endpos);
        if ($startpos > $len-1 || $startpos < 0) {
            trigger_error("substring(), Startindex out of bounds must be 0<n<$len", E_USER_ERROR);
        }
        if ($endpos > $len-1 || $endpos < $startpos) {
            trigger_error("substring(), Endindex out of bounds must be $startpos<n<".($len-1), E_USER_ERROR);
        }
        if ($startpos === $endpos) {
            return (string) $string{$startpos};
        } else {
            $len = $endpos-$startpos;
        }
        return substr($string, $startpos, $len+1);
    }
    
    /**
     * Convert string buffer into array of lines.
     * 
     * @param string $filename
     * @return array string[] lines of file.
     */
    protected static function getLines($buffer) {       
       $lines = preg_split("/\r?\n|\r/", $buffer);
       return $lines;
    }
    
}