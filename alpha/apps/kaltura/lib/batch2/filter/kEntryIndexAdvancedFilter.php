<?php
/**
 * @package api
 * @subpackage filters
 */
class kEntryIndexAdvancedFilter extends kIndexAdvancedFilter
{

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		$this->applyConditionImpl($query, "int_id");
	}	
}
