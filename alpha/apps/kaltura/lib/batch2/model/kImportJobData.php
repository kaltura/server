<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kImportJobData extends kJobData
{
	/**
	 * @var string
	 */
	private $srcFileUrl;
	
	/**
	 * @var string
	 */
	private $destFileLocalPath;
	
	
	/**
	 * @var string
	 */
	private $flavorAssetId;
	
	
	/**
	 * @return the $srcFileUrl
	 */
	public function getSrcFileUrl()
	{
		return $this->srcFileUrl;
	}
	/**
	 * @param $flavorAssetId the $flavorAssetId to set
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}

	/**
	 * @return the $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}


	/**
	 * @return the $destFileLocalPath
	 */
	public function getDestFileLocalPath()
	{
		return $this->destFileLocalPath;
	}

	/**
	 * @param $srcFileUrl the $srcFileUrl to set
	 */
	public function setSrcFileUrl($srcFileUrl)
	{
		$this->srcFileUrl = $srcFileUrl;
	}

	/**
	 * @param $destFileLocalPath the $destFileLocalPath to set
	 */
	public function setDestFileLocalPath($destFileLocalPath)
	{
		$this->destFileLocalPath = $destFileLocalPath;
	}
}
