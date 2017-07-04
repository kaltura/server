<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.order
 */
class ESearchUserOrderByItem extends ESearchOrderByItem
{
    /**
     * @var ESearchUserOrderByFieldName
     */
    protected $sortField;

    /**
     * @return ESearchUserOrderByFieldName
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @param ESearchUserOrderByFieldName $sortField
     */
    public function setSortField($sortField)
    {
        $this->sortField = $sortField;
    }
}
