<?php

/**
 * @package Core
 * @subpackage Batch
 *
 */
class kProvisionJobData
{
	/**
	 * @var string
	 */
	private $streamID;
	
	/**
	 * @var string
	 */
	private $backupStreamID;
	
	/**
	 * @var string
	 */
	private $rtmp;
 	
	/**
	 * @var string
	 */
	private $encoderIP;
 	
	/**
	 * @var string
	 */
	private $backupEncoderIP;
 	
	/**
	 * @var string
	 */
	private $encoderPassword;
 	
	/**
	 * @var string
	 */
	private $encoderUsername;
 	
	/**
	 * @var int
	 */
	private $endDate;
 	
	/**
	 * @var string
	 */
	private $returnVal;
	
	/**
	 * @var int
	 */
	private $mediaType;
	
	/**
	 * @var string
	 */
	private $primaryBroadcastingUrl;
	
	/**
	 * @var string
	 */
	private $secondaryBroadcastingUrl;
	
	/**
	 * @var string
	 */
	private $streamName;
	
	
	/**
	 * @return the $streamID
	 */
	public function getStreamID()
	{
		return $this->streamID;
	}

	/**
	 * @return the $backupStreamID
	 */
	public function getBackupStreamID()
	{
		return $this->backupStreamID;
	}

	/**
	 * @return the $rtmp
	 */
	public function getRtmp()
	{
		return $this->rtmp;
	}

	/**
	 * @return the $encoderIP
	 */
	public function getEncoderIP()
	{
		return $this->encoderIP;
	}

	/**
	 * @return the $backupEncoderIP
	 */
	public function getBackupEncoderIP()
	{
		return $this->backupEncoderIP;
	}

	/**
	 * @return the $encoderPassword
	 */
	public function getEncoderPassword()
	{
		return $this->encoderPassword;
	}

	/**
	 * @return the $encoderUsername
	 */
	public function getEncoderUsername()
	{
		return $this->encoderUsername;
	}

	/**
	 * @return the $endDate
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @return the $returnVal
	 */
	public function getReturnVal()
	{
		return $this->returnVal;
	}
			
	/**
	 * @return the $mediaType
	 */
	public function getMediaType()
	{
		return $this->mediaType;
	}
	
	/**
	 * @return the $primaryBroadcastingUrl
	 */
	public function getPrimaryBroadcastingUrl()
	{
		return $this->primaryBroadcastingUrl;
	}
	
	/**
	 * @return the $secondaryBroadcastingUrl
	 */
	public function getSecondaryBroadcastingUrl()
	{
		return $this->secondaryBroadcastingUrl;
	}
	
	/**
	 * @return the $streamName
	 */
	public function getStreamName()
	{
		return $this->streamName;
	}	

	/**
	 * @param $streamID the $streamID to set
	 */
	public function setStreamID($streamID)
	{
		$this->streamID = $streamID;
	}

	/**
	 * @param $backupStreamID the $backupStreamID to set
	 */
	public function setBackupStreamID($backupStreamID)
	{
		$this->backupStreamID = $backupStreamID;
	}

	/**
	 * @param $rtmp the $rtmp to set
	 */
	public function setRtmp($rtmp)
	{
		$this->rtmp = $rtmp;
	}

	/**
	 * @param $encoderIP the $encoderIP to set
	 */
	public function setEncoderIP($encoderIP)
	{
		$this->encoderIP = $encoderIP;
	}

	/**
	 * @param $backupEncoderIP the $backupEncoderIP to set
	 */
	public function setBackupEncoderIP($backupEncoderIP)
	{
		$this->backupEncoderIP = $backupEncoderIP;
	}

	/**
	 * @param $encoderPassword the $encoderPassword to set
	 */
	public function setEncoderPassword($encoderPassword)
	{
		$this->encoderPassword = $encoderPassword;
	}

	/**
	 * @param $encoderUsername the $encoderUsername to set
	 */
	public function setEncoderUsername($encoderUsername)
	{
		$this->encoderUsername = $encoderUsername;
	}

	/**
	 * @param $endDate the $endDate to set
	 */
	public function setEndDate($endDate)
	{
		$this->endDate = $endDate;
	}

	/**
	 * @param $returnVal the $returnVal to set
	 */
	public function setReturnVal($returnVal)
	{
		$this->returnVal = $returnVal;
	}
	
	/**
	 * @param $mediaType the $mediaType to set
	 */
	public function setMediaType($mediaType)
	{
		$this->mediaType = $mediaType;
	}
	
	/**
	 * @param $primaryBroadcastingUrl the $primaryBroadcastingUrl to set
	 */
	public function setPrimaryBroadcastingUrl($primaryBroadcastingUrl)
	{
		$this->primaryBroadcastingUrl = $primaryBroadcastingUrl;
	}
	
	/**
	 * @param $secondaryBroadcastingUrl the $secondaryBroadcastingUrl to set
	 */
	public function setSecondaryBroadcastingUrl($secondaryBroadcastingUrl)
	{
		$this->secondaryBroadcastingUrl = $secondaryBroadcastingUrl;
	}
	
	/**
	 * @param $streamName the $streamName to set
	 */
	public function setStreamName($streamName)
	{
		$this->streamName = $streamName;
	}	
	
}

?>