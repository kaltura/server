<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kExclusiveLockKey
{
	/**
	 * @var int
	 */
	private $schedulerId;
	
    
	/**
	 * @var int
	 */
	private $workerId;
	
    
	/**
	 * @var int
	 */
	private $batchIndex;
	
	
	
	/**
	 * @return the $schedulerId
	 */
	public function getSchedulerId()
	{
		return $this->schedulerId;
	}

	/**
	 * @return the $workerId
	 */
	public function getWorkerId()
	{
		return $this->workerId;
	}

	/**
	 * @return the $batchIndex
	 */
	public function getBatchIndex()
	{
		return $this->batchIndex;
	}

	/**
	 * @param $schedulerId the $schedulerId to set
	 */
	public function setSchedulerId($schedulerId)
	{
		$this->schedulerId = $schedulerId;
	}

	/**
	 * @param $workerId the $workerId to set
	 */
	public function setWorkerId($workerId)
	{
		$this->workerId = $workerId;
	}

	/**
	 * @param $batchIndex the $batchIndex to set
	 */
	public function setBatchIndex($batchIndex)
	{
		$this->batchIndex = $batchIndex;
	}

	
    
}

?>