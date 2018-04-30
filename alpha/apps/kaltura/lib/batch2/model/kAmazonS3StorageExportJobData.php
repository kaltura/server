<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAmazonS3StorageExportJobData extends kStorageExportJobData  
{
	 /**
	 * @var KalturaAmazonS3StorageProfileFilesPermissionLevel
	 */   	
    private $filesPermissionInS3;
    
	 /**
	 * @var string
	 */   	
    private $s3Region;
	
	/**
	* $var string
	*/
	private $sseType;
	
	/**
	* $var string
	*/
	private $sseKmsKeyId;
    
	/**
	* $var string
	*/
	private $signatureType;
    
		/**
	* $var string
	*/
	private $endPoint;
    
	public function setStorageExportJobData(StorageProfile $externalStorage, FileSync $fileSync, $srcFileSync, $force = false)
	{
		parent::setStorageExportJobData($externalStorage, $fileSync, $srcFileSync);
		$this->setFilesPermissionInS3($externalStorage->getFilesPermissionInS3());
		$this->setS3Region($externalStorage->getS3Region());
		$this->setSseType($externalStorage->getSseType());
		$this->setSseKmsKeyId($externalStorage->getSseKmsKeyId());
		$this->setSignatureType($externalStorage->getSignatureType());
		$this->setEndPoint($externalStorage->getEndPoint());
	}

	/**
	 * @return the $filesPermissionInS3
	 */
	public function getFilesPermissionInS3()
	{
		return $this->filesPermissionInS3;
	}
	
	/**
	 * @param $filesPermissionInS3 the $filesPermissionInS3 to set
	 */
	public function setFilesPermissionInS3($filesPermissionInS3)
	{
		$this->filesPermissionInS3 = $filesPermissionInS3;	
	}	

	/**
	 * @return the $s3Region
	 */
	public function getS3Region()
	{
		return $this->s3Region;
	}
	
	/**
	 * @param $s3Region the $s3Region to set
	 */
	public function setS3Region($s3Region)
	{
		$this->s3Region = $s3Region;	
	}	
	
	/**
	 * @return the $sseType
	 */
	public function getSseType()
	{
		return $this->sseType;
	}
	
	/**
	 * @param $sseType the $sseType to set
	 */
	public function setSseType($sseType)
	{
		$this->sseType = $sseType;	
	}	
	
	/**
	 * @return the $sseKmsKeyId
	 */
	public function getSseKmsKeyId()
	{
		return $this->sseKmsKeyId;
	}
	
	/**
	 * @param $sseKmsKeyId the $sseKmsKeyId to set
	 */
	public function setSseKmsKeyId($sseKmsKeyId)
	{
		$this->sseKmsKeyId = $sseKmsKeyId;	
	}
		
	/**
	 * @return the signature type
	 */
	public function getSignatureType()
	{
		return $this->signatureType;
	}
	
	/**
	 * @param $signatureType the signatureType to set
	 */
	public function setSignatureType($signatureType)
	{
		$this->signatureType = $signatureType;	
	}	
	/**
	 * @return the endPoint
	 */
	public function getEndPoint()
	{
		return $this->endPoint;
	}
	
	/**
	 * @param $endPoint the endPoint to set
	 */
	public function setEndPoint($endPoint)
	{
		$this->endPoint = $endPoint;	
	}	
}