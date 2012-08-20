<?php
/*
 *  $Id: IndexInfo.php,v 1.7 2005/02/25 15:47:02 pachanga Exp $
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
 * Represents an index.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.7 $
 * @package   creole.metadata
 */
class IndexInfo {

    /** name of the index */
    private $name;

    /** columns in this index */
    private $columns = array();

    /** uniqueness flag */
    private $isUnique = false;

    /** additional vendor specific information */
    private $vendorSpecificInfo = array();

    function __construct($name, $isUnique = false, $vendorInfo = array())
    {
        $this->name = $name;
        $this->isUnique = $isUnique;
        $this->vendorSpecificInfo = $vendorInfo;
    }

    public function isUnique()
    {
        return $this->isUnique;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Get vendor specific optional information for this index.
     * @return array vendorSpecificInfo[]
     */
    public function getVendorSpecificInfo()
    {
        return $this->vendorSpecificInfo;
    }

    public function addColumn($column)
    {
        $this->columns[] = $column;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function toString()
    {
        return $this->name;
    }

}
