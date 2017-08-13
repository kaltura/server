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

	/**
	 * @var string
	 */
	protected $depthGreaterThanEqual = null;

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		$this->applyConditionImpl($query, "id");
	}
	
	public function applyConditionImpl(IKalturaDbQuery $query, $ColumnName)
	{
		if (is_null($this->indexIdGreaterThan))
			return;
		
		if($query instanceof IKalturaIndexQuery)
		{
			if(is_null($this->depthGreaterThanEqual))
				$query->addColumnWhere($ColumnName, $this->indexIdGreaterThan, Criteria::GREATER_THAN);
			else
				$query->addCondition('( (('.$ColumnName.' '.Criteria::GREATER_THAN.' '. $this->indexIdGreaterThan.') and (depth '.Criteria::EQUAL.' '.$this->depthGreaterThanEqual.')) or (depth'.Criteria::GREATER_THAN.' '.$this->depthGreaterThanEqual.') )');
		}
		
		elseif($query instanceof Criteria)
		$query->add($ColumnName, $this->indexIdGreaterThan, Criteria::GREATER_THAN);
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

	/**
	 * @return string $depthGreaterThanEqual
	 */
	public function getDepthGreaterThanEqual()
	{
		return $this->depthGreaterThanEqual;
	}

	/**
	 * @param string $depthGreaterThanEqual
	 */
	public function setDepthGreaterThanEqual($depthGreaterThanEqual)
	{
		$this->depthGreaterThanEqual = $depthGreaterThanEqual;
	}
}
