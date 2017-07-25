<?php
/**
 * @package api
 * @subpackage filters
 */
class kIndexAdvancedFilter extends AdvancedSearchFilterItem
{
	const DEFAULT_ID_COLUMN_NAME = "id"; 
	
	/**
 	 * @var int
 	 */
	protected $indexIdGreaterThan = null;

	/**
	 * @var string
	 */
	public $idColumnName = null;
	
	/**
	 * @var int
	 */
	protected $depthGreaterThanEqual = null;

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		if (is_null($this->indexIdGreaterThan))
			return;

		if($query instanceof IKalturaIndexQuery)
		{
			if(is_null($this->depthGreaterThanEqual))
				$query->addColumnWhere($this->getIdColumnName(), $this->indexIdGreaterThan, Criteria::GREATER_THAN);
			else
				$query->addCondition('( ((' . $this->getIdColumnName().' '.Criteria::GREATER_THAN.' '. $this->indexIdGreaterThan.') and (depth '.Criteria::EQUAL.' '.$this->depthGreaterThanEqual.')) or (depth'.Criteria::GREATER_THAN.' '.$this->depthGreaterThanEqual.') )');
		}

		elseif($query instanceof Criteria)
			$query->add($this->getIdColumnName(), $this->indexIdGreaterThan, Criteria::GREATER_THAN);
	}
	
	/**
     * @return int $indexIdGreaterThan
     */
	public function getIndexIdGreaterThan()
	{
		return $this->indexIdGreaterThan;
	}

	/**
	 * @param int $indexIdGreaterThan
	 */
	public function setIndexIdGreaterThan($indexIdGreaterThan)
	{
		$this->indexIdGreaterThan = $indexIdGreaterThan;
	}

	/**
	 * @return int $depthGreaterThanEqual
	 */
	public function getDepthGreaterThanEqual()
	{
		return $this->depthGreaterThanEqual;
	}

	/**
	 * @param int $depthGreaterThanEqual
	 */
	public function setDepthGreaterThanEqual($depthGreaterThanEqual)
	{
		$this->depthGreaterThanEqual = $depthGreaterThanEqual;
	}
	
	/**
	 * @return string $idColumnName
	 */
	public function getIdColumnName()
	{
		if(!$this->idColumnName)
			return self::DEFAULT_ID_COLUMN_NAME;
		
		return $this->idColumnName;
	}
	
	/**
	 * @param string $idColumnName
	 */
	public function setIdColumnName($idColumnName)
	{
		$this->idColumnName = $idColumnName;
	}
}
