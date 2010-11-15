<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kBulkDownloadJobData extends kJobData
{
	/**
	 * Comma separated list of entry ids
	 * 
	 * @var string
	 */
	private $entryIds;
	
	/**
	 * Flavor params id to use for conversion
	 * 
	 * @var int
	 */
	private $flavorParamsId;
	
	/**
	 * The id of the requesting user
	 * 
	 * @var string
	 */
	private $puserId;
	
	public function getEntryIds()
	{
		return $this->entryIds;
	}
	/**
	 * @param $puserId the $puserId to set
	 */
	public function setPuserId($puserId)
	{
		$this->puserId = $puserId;
	}

	/**
	 * @return the $puserId
	 */
	public function getPuserId()
	{
		return $this->puserId;
	}


	public function getFlavorParamsId()
	{
		return $this->flavorParamsId;
	}

	public function setEntryIds($entryIds)
	{
		$this->entryIds = $entryIds;
	}
	
	public function setFlavorParamsId($flavorParamsId)
	{
		$this->flavorParamsId = $flavorParamsId;
	}
}
