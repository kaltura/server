<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBaseEntryCloneOptionComponent extends kBaseEntryCloneOptionItem
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @var int itemType - critera is a collection of properties of the entry. The list of properties of the same
     *                      criteria are not always copied during the clone operation
     */
    protected $itemType;

    /**
     * @return int
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * @param int $itemType
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;
    }

    /**
     * @var bool - rule properties should be cloned yes/no
     */
    protected $rule = true;

    /**
     * @return boolean
     */
    public function isRule()
    {
        return $this->rule;
    }

    /**
     * @param boolean $rule
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
    }
}
