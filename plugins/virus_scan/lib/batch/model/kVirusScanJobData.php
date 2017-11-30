<?php
/**
 * @package plugins.virusScan
 * @subpackage model.data
 */
class kVirusScanJobData extends kJobData
{
	/**
	 * @var FileContainer
	 */
	private $fileContainer;
	
	/**
	 * @var string
	 */
	private $flavorAssetId;
	
	/**
	 * @var KalturaVirusScanJobResult
	 */
	private $scanResult;
	
	/**
	 * @var KalturaVirusFoundAction
	 */
	private $virusFoundAction;
	
	/**
	 * @return FileContainer $fileData
	 */
	public function getFileContainer()
	{
		return $this->fileContainer;
	}

	/**
	 * @return the $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}

	/**
	 * @return the $scanResult
	 */
	public function getScanResult()
	{
		return $this->scanResult;
	}
	
	/**
	 * @return the $virusFoundAction
	 */
	public function getVirusFoundAction()
	{
		return $this->virusFoundAction;
	}

	/**
	 * @param FileContainer $fileContainer
	 */
	public function setFileContainer($fileContainer)
	{
		$this->fileContainer = $fileContainer;
	}

	/**
	 * @param $flavorAssetId the $flavorAssetId to set
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}

	/**
	 * @param $scanReulst the $scanReulst to set
	 */
	public function setScanResult($scanResult)
	{
		$this->scanResult = $scanResult;
	}
	
	/**
	 * @param $virusFoundAction the $virusFoundAction to set
	 */
	public function setVirusFoundAction($virusFoundAction)
	{
		$this->virusFoundAction = $virusFoundAction;
	}
}
