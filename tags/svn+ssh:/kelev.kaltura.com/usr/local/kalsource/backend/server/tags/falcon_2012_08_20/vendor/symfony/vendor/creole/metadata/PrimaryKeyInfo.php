<?php
/*
 *  $Id: PrimaryKeyInfo.php,v 1.6 2005/02/25 15:47:02 pachanga Exp $
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
 * Represents a PrimaryKey
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.6 $
 * @package   creole.metadata
 */
class PrimaryKeyInfo {

    /** name of the primary key */
    private $name;

    /** columns in the primary key */
    private $columns = array();

    /** additional vendor specific information */
    private $vendorSpecificInfo = array();

    /**
     * @param string $name The name of the foreign key.
     */
    function __construct($name, $vendorInfo = array())
    {
        $this->name = $name;
        $this->vendorSpecificInfo = $vendorInfo;
    }

    /**
     * Get foreign key name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Column $column
     * @return void
     */
    public function addColumn($column)
    {
        $this->columns[] = $column;
    }

    /**
     * @return array Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get vendor specific optional information for this primary key.
     * @return array vendorSpecificInfo[]
     */
    public function getVendorSpecificInfo()
    {
        return $this->vendorSpecificInfo;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->name;
    }
}
