<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.order
 */
class ESearchCategoryOrderByItem extends ESearchOrderByItem
{
    /**
     * @var ESearchCategoryOrderByFieldName
     */
    protected $sortField;

    /**
     * @return ESearchCategoryOrderByFieldName
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @param ESearchCategoryOrderByFieldName $sortField
     */
    public function setSortField($sortField)
    {
        $this->sortField = $sortField;
    }
    
}
