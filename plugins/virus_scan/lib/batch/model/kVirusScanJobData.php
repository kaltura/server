<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kVirusScanJobData extends kJobData
{
	/**
	 * @var string
	 */
	private $srcFilePath;
	
	/**
	 * @var string
	 */
	private $flavorAssetId;
	
	/**
	 * @var bool
	 */
	private $infected;
	
	/**
	 * @return the $srcFilePath
	 */
	public function getSrcFilePath()
	{
		return $this->srcFilePath;
	}

	/**
	 * @return the $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}

	/**
	 * @return the $infected
	 */
	public function getInfected()
	{
		return $this->infected;
	}

	/**
	 * @param $srcFilePath the $srcFilePath to set
	 */
	public function setSrcFilePath($srcFilePath)
	{
		$this->srcFilePath = $srcFilePath;
	}

	/**
	 * @param $flavorAssetId the $flavorAssetId to set
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}

	/**
	 * @param $infected the $infected to set
	 */
	public function setInfected($infected)
	{
		$this->infected = $infected;
	}
}
