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
	 * @see AdvancedSearchFilterItem::apply()
	 */
	public function apply(baseObjectFilter $filter, IKalturaDbQuery $query)
	{
		$query->addColumnWhere('id', $this->indexIdGreaterThanOrEqual, Criteria::GREATER_THAN);
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
