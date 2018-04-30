<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.order
 */
abstract class ESearchOrderByItem extends BaseObject
{

    const ORDER = 'order';

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

    abstract public function getSortField();

    public function getSortConditions()
    {
        $conditions = array();

        $conditions[] = array(
            $this->getSortField() => array(self::ORDER => $this->getSortOrder())
        );
        return $conditions;
    }

}
