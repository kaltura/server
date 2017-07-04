<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.order
 */
class ESearchEntryOrderByItem extends ESearchOrderByItem
{
    /**
     * @var ESearchEntryOrderByFieldName
     */
    protected $sortField;

    /**
     * @return ESearchEntryOrderByFieldName
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @param ESearchEntryOrderByFieldName $sortField
     */
    public function setSortField($sortField)
    {
        $this->sortField = $sortField;
    }
    
        
}
