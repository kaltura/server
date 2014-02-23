<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kFileSyncDescriptor 
{
	/**
	 * @var string
	 */
	private $fileSyncLocalPath;
	
	/**
	 * @var string
	 */
	private $fileSyncRemoteUrl;
	
	/**
	 * 
	 * @var int
	 */
	private $fileSyncObjectSubType;
	
	/**
	 * @return the $fileSyncLocalPath
	 */
	public function getFileSyncLocalPath() 
	{
		return $this->fileSyncLocalPath;
	}

	/**
	 * @return the $fileSyncRemoteUrl
	 */
	public function getFileSyncRemoteUrl() 
	{
		return $this->fileSyncRemoteUrl;
	}

	/**
	 * @param string $fileSyncLocalPath
	 */
	public function setFileSyncLocalPath($fileSyncLocalPath) 
	{
		$this->fileSyncLocalPath = $fileSyncLocalPath;
	}

	/**
	 * @param string $fileSyncRemoteUrl
	 */
	public function setFileSyncRemoteUrl($fileSyncRemoteUrl) 
	{
		$this->fileSyncRemoteUrl = $fileSyncRemoteUrl;
	}
		
	/**
	 * @return the $fileSyncObjectSubType
	 */
	public function getFileSyncObjectSubType() {
		return $this->fileSyncObjectSubType;
	}
	
	/**
	 * @param int $fileSyncObjectSubType
	 */
	public function setFileSyncObjectSubType($fileSyncObjectSubType) {
		$this->fileSyncObjectSubType = $fileSyncObjectSubType;
	}	
}