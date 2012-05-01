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
	protected $indexIdGreaterThan = null;

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::apply()
	 */
	public function apply(baseObjectFilter $filter, IKalturaDbQuery $query)
	{
		if (!is_null($this->indexIdGreaterThan))
			$query->addColumnWhere('id', $this->indexIdGreaterThan, Criteria::GREATER_THAN);
	}
	
	/**
	 * @return string $indexIdGreaterThan
	 */
	public function getIndexIdGreaterThan()
	{
		return $this->indexIdGreaterThan;
	}

	/**
	 * @param string $indexIdGreaterThan
	 */
	public function setIndexIdGreaterThan($indexIdGreaterThan)
	{
		$this->indexIdGreaterThan = $indexIdGreaterThan;
	}

	
}
