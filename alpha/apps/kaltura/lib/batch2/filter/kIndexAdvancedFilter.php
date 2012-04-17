<?php
/**
 * @package api
 * @subpackage filters
 */
class kIndexAdvancedFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var string
	 */
	protected $indexIdGreaterThanOrEqual = null;
	
	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterOperator::applyCondition()
	 */
	public function applyCondition(IKalturaIndexQuery $query)
	{
		$query->addWhere("id > {$this->indexIdGreaterThanOrEqual}");
	}
	
	/**
	 * @return string $indexIdGreaterThanOrEqual
	 */
	public function getIndexIdGreaterThanOrEqual()
	{
		return $this->indexIdGreaterThanOrEqual;
	}

	/**
	 * @param string $indexIdGreaterThanOrEqual
	 */
	public function setIndexIdGreaterThanOrEqual($indexIdGreaterThanOrEqual)
	{
		$this->indexIdGreaterThanOrEqual = $indexIdGreaterThanOrEqual;
	}

	
}
