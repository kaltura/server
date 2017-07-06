<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.order
 */
abstract class ESearchOrderByItem extends BaseObject
{
    /**
     * @var ESearchSortOrder
     */
    protected $sortOrder;

    /**
     * @return ESearchSortOrder
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param ESearchSortOrder $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }
    
}
