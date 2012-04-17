<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kIndexJobData extends kJobData
{
	/**
	 * @var baseObjectFilter
	 */
	private $filter;
	
	/**
	 * @var int
	 */
	private $lastIndexId;
	
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
}
