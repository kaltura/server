<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kIndexJobData extends kJobData
{
	/**
	 * The filter should return the list of objects that need to be reindexed.
	 * @var baseObjectFilter
	 */
	private $filter;
	
	/**
	 * Indicates the last id that reindexed, used when the batch crached, to re-run from the last crash point.
	 * @var int
	 */
	private $lastIndexId;


	/**
	 * Indicates the last depth that reindexed, used when the batch crached, to re-run from the last crash point.
	 * @var int
	 */
	private $lastIndexDepth;
	
	/**
	 * Indicates that the object columns and attributes values should be recalculated before reindexed.
	 * @var bool
	 */
	private $shouldUpdate;
	
	/**
	 * Indicates that the job locked .
	 * @var kFeatureStatus
	 */
	private $featureStatusesToRemove = array();
	
	/**
	 * @return baseObjectFilter $filter
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/**
	 * @param baseObjectFilter $filter
	 */
	public function setFilter(baseObjectFilter $filter)
	{
		$this->filter = $filter;
	}
	
	/**
	 * @return int $lastIndexId
	 */
	public function getLastIndexId()
	{
		return $this->lastIndexId;
	}

	/**
	 * @param int $lastIndexId
	 */
	public function setLastIndexId($lastIndexId)
	{
		$this->lastIndexId = $lastIndexId;
	}

	/**
	 * @return int $lastIndexDepth
	 */
	public function getLastIndexDepth()
	{
		return $this->lastIndexDepth;
	}

	/**
	 * @param int $lastIndexDepth
	 */
	public function setLastIndexDepth($lastIndexDepth)
	{
		$this->lastIndexDepth = $lastIndexDepth;
	}
	
	/**
	 * @return bool $shouldUpdate
	 */
	public function getShouldUpdate()
	{
		return $this->shouldUpdate;
	}

	/**
	 * @param bool $shouldUpdate
	 */
	public function setShouldUpdate($shouldUpdate)
	{
		$this->shouldUpdate = $shouldUpdate;
	}
	
	/**
	 * @return int
	 */
	public function getFeatureStatusesToRemove()
	{
		return $this->featureStatusesToRemove;
	}

	/**
	 * @param array $featureStatusesToRemove
	 */
	public function setFeatureStatusesToRemove($featureStatusesToRemove)
	{
		$this->featureStatusesToRemove = $featureStatusesToRemove;
	}
}
