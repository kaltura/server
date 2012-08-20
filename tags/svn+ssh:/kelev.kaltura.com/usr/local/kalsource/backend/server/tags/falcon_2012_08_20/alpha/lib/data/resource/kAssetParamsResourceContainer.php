<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAssetParamsResourceContainer extends kResource 
{
	/**
	 * The content resource to associate with asset params
	 * @var kContentResource
	 */
	private $resource;
	
	/**
	 * The asset params to associate with the reaource
	 * @var int
	 */
	private $assetParamsId;
	
	/**
	 * @return kContentResource
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @return int
	 */
	public function getAssetParamsId()
	{
		return $this->assetParamsId;
	}

	/**
	 * @param kContentResource $resource
	 */
	public function setResource(kContentResource $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * @param int $assetParamsId
	 */
	public function setAssetParamsId($assetParamsId)
	{
		$this->assetParamsId = $assetParamsId;
	}
}