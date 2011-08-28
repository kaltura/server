<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kConvertJobData extends kConvartableJobData
{
	const CONVERSION_MILTI_COMMAND_LINE_SEPERATOR = ';';
	const CONVERSION_FAST_START_SIGN = 'FS';


	/**
	 * @var string
	 */
	private $destFileSyncLocalPath;

	/**
	 * @var string
	 */
	private $destFileSyncRemoteUrl;

	/**
	 * @var string
	 */
	private $logFileSyncLocalPath;

	/**
	 * @var string
	 */
	private $logFileSyncRemoteUrl;

	/**
	 * @var string
	 */
	private $flavorAssetId;

	/**
	 * @var string
	 */
	private $remoteMediaId;

	/**
	 * @return the $destFileSyncLocalPath
	 */
	public function getDestFileSyncLocalPath()
	{
		return $this->destFileSyncLocalPath;
	}

	/**
	 * @return the $logFileSyncLocalPath
	 */
	public function getLogFileSyncLocalPath()
	{
		return $this->logFileSyncLocalPath;
	}

	/**
	 * @param $remoteMediaId the $remoteMediaId to set
	 */
	public function setRemoteMediaId($remoteMediaId)
	{
		$this->remoteMediaId = $remoteMediaId;
	}

	/**
	 * @return the $remoteMediaId
	 */
	public function getRemoteMediaId()
	{
		return $this->remoteMediaId;
	}

	/**
	 * @param $destFileSyncRemoteUrl the $destFileSyncRemoteUrl to set
	 */
	public function setDestFileSyncRemoteUrl($destFileSyncRemoteUrl)
	{
		$this->destFileSyncRemoteUrl = $destFileSyncRemoteUrl;
	}

	/**
	 * @param $logFileSyncRemoteUrl the $logFileSyncRemoteUrl to set
	 */
	public function setLogFileSyncRemoteUrl($logFileSyncRemoteUrl)
	{
		$this->logFileSyncRemoteUrl = $logFileSyncRemoteUrl;
	}

	/**
	 * @return the $destFileSyncRemoteUrl
	 */
	public function getDestFileSyncRemoteUrl()
	{
		return $this->destFileSyncRemoteUrl;
	}

	/**
	 * @return the $logFileSyncRemoteUrl
	 */
	public function getLogFileSyncRemoteUrl()
	{
		return $this->logFileSyncRemoteUrl;
	}


	/**
	 * @return the $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}

	/**
	 * @param $destFileSyncLocalPath the $destFileSyncLocalPath to set
	 */
	public function setDestFileSyncLocalPath($destFileSyncLocalPath)
	{
		$this->destFileSyncLocalPath = $destFileSyncLocalPath;
	}

	/**
	 * @param $logFileSyncLocalPath the $logFileSyncLocalPath to set
	 */
	public function setLogFileSyncLocalPath($logFileSyncLocalPath)
	{
		$this->logFileSyncLocalPath = $logFileSyncLocalPath;
	}

	/**
	 * @param $flavorAssetId the $flavorAssetId to set
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}
}
