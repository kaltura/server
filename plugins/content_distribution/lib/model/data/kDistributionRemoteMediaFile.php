<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.data
 */
class kDistributionRemoteMediaFile extends KalturaObject
{
	/**
	 * @var string
	 */
	private $version;
	
	/**
	 * @var string
	 */
	private $assetId;
	
	/**
	 * @var string
	 */
	private $remoteId;
	
	/**
	 * @return the $version
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @return the $assetId
	 */
	public function getAssetId()
	{
		return $this->assetId;
	}

	/**
	 * @return the $remoteId
	 */
	public function getRemoteId()
	{
		return $this->remoteId;
	}

	/**
	 * @param string $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}

	/**
	 * @param string $assetId
	 */
	public function setAssetId($assetId)
	{
		$this->assetId = $assetId;
	}

	/**
	 * @param string $remoteId
	 */
	public function setRemoteId($remoteId)
	{
		$this->remoteId = $remoteId;
	}
}
