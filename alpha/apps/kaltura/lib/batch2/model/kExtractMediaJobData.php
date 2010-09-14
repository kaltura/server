<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kExtractMediaJobData extends kConvartableJobData
{
	/**
	 * @var string
	 */
	private $flavorAssetId;
	
	
	/**
	 * @return the $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}

	/**
	 * @param $flavorAssetId the $flavorAssetId to set
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}

	
}

?>