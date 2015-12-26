<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBaseEntryCloneOptionComponent extends kBaseEntryCloneOptionItem
{
    /**
     * @var int itemType - critera is a collection of properties of the entry. The list of properties of the same
     *                      criteria are not always copied during the clone operation
     */
    protected $itemType;

    /**
     * @var CloneComponentSelectorType - rule properties should be cloned yes/no
     */
    protected $rule = CloneComponentSelectorType::INCLUDE_COMPONENT;

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
     * @return CloneComponentSelectorType
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @param CloneComponentSelectorType $rule
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
    }
}
