<?php
/*
 *  $Id: ODBCStatement.php,v 1.1 2004/07/27 23:08:30 hlellelid Exp $
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

require_once 'creole/Statement.php';
require_once 'creole/common/StatementCommon.php';

/**
 * ODBC Statement
 *
 * @author    Dave Lawson <dlawson@masterytech.com>
 * @version   $Revision: 1.1 $
 * @package   creole.drivers.odbc
 */
class ODBCStatement extends StatementCommon implements Statement
{
    /**
     * @see Statement::executeQuery()
     */
    public function executeQuery($sql, $fetchmode = null)
    {
        if ($this->resultSet)
        {
            $this->resultSet->close();
            $this->resultSet = null;
        }

        $this->updateCount = null;

        if ($this->conn->getAdapter()->hasLimitOffset())
        {
            if ($this->limit > 0 || $this->offset > 0)
                $this->conn->applyLimit($sql, $this->offset, $this->limit);
        }

        $this->resultSet = $this->conn->executeQuery($sql, $fetchmode);

        if (!$this->conn->getAdapter()->hasLimitOffset())
        {
            $this->resultSet->_setOffset($this->offset);
            $this->resultSet->_setLimit($this->limit);
        }

        return $this->resultSet;
    }

}