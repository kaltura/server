<?php


class kOneDriveImportJobData extends kDropFolderImportJobData
{
	/**
	 * @var int
	 */
	private $vendorIntegrationId;
	
	/**
	 * @var int
	 */
	private $expiry;
	
	/**
	 * @var string
	 */
	private $itemId;
	
	/**
	 * @var string
	 */
	private $driveId;
	
	
	/**
	 * @return int
	 */
	public function getVendorIntegrationId()
	{
		return $this->vendorIntegrationId;
	}
	
	/**
	 * @param int $vendorIntegrationId
	 */
	public function setVendorIntegrationId ($vendorIntegrationId)
	{
		$this->vendorIntegrationId = $vendorIntegrationId;
	}
	
	/**
	 * @return int
	 */
	public function getExpiry()
	{
		return $this->expiry;
	}
	
	/**
	 * @param int $expiry
	 */
	public function setExpiry ($expiry)
	{
		$this->expiry = $expiry;
	}
	
	/**
	 * @return int
	 */
	public function getItemId()
	{
		return $this->itemId;
	}
	
	/**
	 * @param int $itemId
	 */
	public function setItemId($itemId)
	{
		$this->itemId = $itemId;
	}
	
	/**
	 * @return int
	 */
	public function getDriveId()
	{
		return $this->driveId;
	}
	
	/**
	 * @param int $driveId
	 */
	public function setDriveId($driveId)
	{
		$this->driveId = $driveId;
	}
	
	/**
	 * @param int $dropFolderFileId
	 */
	public function setDropFolderFileId($dropFolderFileId)
	{
		parent::setDropFolderFileId($dropFolderFileId);
		
		$dropFolderFile = DropFolderFilePeer::retrieveByPK($dropFolderFileId);
		if ($dropFolderFile instanceof OneDriveDropFolderFile)
		{
			$this->itemId = $dropFolderFile->getRemoteId();
			$this->driveId = $dropFolderFile->getDriveId();
			$this->expiry = $dropFolderFile->getTokenExpiry();
		}
	}
}