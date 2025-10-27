<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class ESearchParams extends BaseObject
{
	/**
	 * @var ESearchOperator
	 */
	protected $searchOperator;

	/**
	 * @var string
	 */
	protected $objectStatuses;

	/**
	 * @var string
	 */
	protected $objectId;

	/**
	 * @var ESearchOrderBy
	 */
	protected $orderBy;

	/**
	 * @var bool
	 */
	protected $useHighlight;

	/**
	 * @var ESearchAggregations
	 */
	protected $aggregations;
	
	/**
	 * @var bool
	 */
	protected $ignoreSynonym;

	/**
	 * @var string
	 */
	protected $objectIds;

	/**
	 * @var bool
	 */
	protected $objectIdsNotIn;

	/**
	 * @var ESearchScoreFunctionParams
	 */
	protected $scoreFunctionParams;



	/**
	 * @return ESearchOperator
	 */
	public function getSearchOperator()
	{
		return $this->searchOperator;
	}

	/**
	 * @param ESearchOperator $searchOperator
	 */
	public function setSearchOperator($searchOperator)
	{
		$this->searchOperator = $searchOperator;
	}

	/**
	 * @return string
	 */
	public function getObjectStatuses()
	{
		return $this->objectStatuses;
	}

	/**
	 * @param string $objectStatuses
	 */
	public function setObjectStatuses($objectStatuses)
	{
		$this->objectStatuses = $objectStatuses;
	}

	/**
	 * @return string
	 */
	public function getObjectId()
	{
		return isset($this->objectId) ? $this->objectId : $this->objectIds;
	}

	/**
	 * @param string $objectId
	 */
	public function setObjectId($objectId)
	{
		$this->objectId = $objectId;
	}

	public function setObjectIds($objectIds)
	{
		$this->objectIds = $objectIds;
	}

	/**
	 * @return bool
	 */
	public function getObjectIdsNotIn()
	{
		return $this->objectIdsNotIn;
	}

	/**
	 * @param bool $objectIdsNotIn
	 */
	public function setObjectIdsNotIn($objectIdsNotIn)
	{
		$this->objectIdsNotIn = $objectIdsNotIn;
	}

	/**
	 * @return ESearchOrderBy
	 */
	public function getOrderBy()
	{
		return $this->orderBy;
	}

	/**
	 * @param ESearchOrderBy $orderBy
	 */
	public function setOrderBy($orderBy)
	{
		$this->orderBy = $orderBy;
	}

	/**
	 * @return bool
	 */
	public function getUseHighlight()
	{
		return $this->useHighlight;
	}

	/**
	 * @param bool $useHighlight
	 */
	public function setUseHighlight($useHighlight)
	{
		$this->useHighlight = $useHighlight;
	}

	/***
	 * @param ESearchAggregations $aggregations
	 */
	public function setAggregations($aggregations)
	{
		$this->aggregations = $aggregations;
	}

	/***
	 * @return ESearchAggregations
	 */
	public function getAggregations()
	{
		return $this->aggregations;
	}
	
	/**
	 * @return bool
	 */
	public function getIgnoreSynonym()
	{
		return $this->ignoreSynonym;
	}
	
	/**
	 * @param bool $ignoreSynonym
	 */
	public function setIgnoreSynonym($ignoreSynonym)
	{
		$this->ignoreSynonym = $ignoreSynonym;
	}

	/**
	 * @param ESearchScoreFunctionParams $scoreFunctionParams
	 * @return void
	 */
	public function setScoreFunctionParams($scoreFunctionParams)
	{
		$this->scoreFunctionParams = $scoreFunctionParams;
	}

	/**
	 * @return ESearchScoreFunctionParams
	 */
	public function getScoreFunctionParams()
	{
		return $this->scoreFunctionParams;
	}
}
