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
		if (is_null($this->indexIdGreaterThan) && is_null($this->depthGreaterThanEqual))
			return;

		if (!is_null($this->indexIdGreaterThan) && is_null($this->depthGreaterThanEqual))
		{
			if($query instanceof IKalturaIndexQuery)
				$query->addColumnWhere('id', $this->indexIdGreaterThan, Criteria::GREATER_THAN);

			elseif($query instanceof Criteria)
				$query->add('id', $this->indexIdGreaterThan, Criteria::GREATER_THAN);

		}
		else
		{
			if($query instanceof IKalturaIndexQuery)
				$query->addCondition('( ((id '.Criteria::GREATER_THAN.' '. $this->indexIdGreaterThan.') and (depth '.Criteria::EQUAL.' '.$this->depthGreaterThanEqual.')) or (depth'.Criteria::GREATER_THAN.' '.$this->depthGreaterThanEqual.') )');

			elseif($query instanceof Criteria)
			{
				$query->add('id', $this->indexIdGreaterThan, Criteria::GREATER_THAN);
				$query->add('depth', $this->depthGreaterThanEqual, Criteria::EQUAL);
				$query->addOr('depth',$this->depthGreaterThanEqual,Criteria::GREATER_THAN);
			}

		}



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
