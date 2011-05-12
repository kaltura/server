<?php
/*
 *  $Id: SQLitePreparedStatement.php,v 1.7 2004/03/20 04:16:50 hlellelid Exp $
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
 
require_once 'creole/PreparedStatement.php';
require_once 'creole/common/PreparedStatementCommon.php';

/**
 * MySQL subclass for prepared statements.
 * 
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.7 $
 * @package   creole.drivers.sqlite
 */
class SQLitePreparedStatement extends PreparedStatementCommon implements PreparedStatement {
    
    /**
     * Quotes string using native sqlite_escape_string() function.
     * @see ResultSetCommon::escape()
     */
    protected function escape($str)
    {
        return sqlite_escape_string($str);
    }
    
    /**
     * Applies sqlite_udf_encode_binary() to ensure that binary contents will be handled correctly by sqlite.
     * @see PreparedStatement::setBlob()
     * @see ResultSet::getBlob()
     */
    function setBlob($paramIndex, $blob) 
    {    
        if ($blob === null) {
            $this->setNull($paramIndex);
        } else {
            // they took magic __toString() out of PHP5.0.0; this sucks
            if (is_object($blob)) {
                $blob = $blob->__toString();
            }
            $this->boundInVars[$paramIndex] = "'" . sqlite_udf_encode_binary( $blob ) . "'";
        }
    }
    
}
