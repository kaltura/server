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
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		if (is_null($this->indexIdGreaterThan))
			return;
			
		if($query instanceof IKalturaIndexQuery)
			$query->addColumnWhere('id', $this->indexIdGreaterThan, Criteria::GREATER_THAN);
		elseif($query instanceof Criteria)
			$query->add('id', $this->indexIdGreaterThan, Criteria::GREATER_THAN);
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
