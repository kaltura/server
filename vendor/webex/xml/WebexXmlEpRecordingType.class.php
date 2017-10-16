<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpRecordingType extends WebexXmlRequestType
{
	/**
	 *
	 * @var int
	 */
	protected $recordingID;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 *
	 * @var string
	 */
	protected $description;
	
	/**
	 *
	 * @var string
	 */
	protected $createTime;
	
	/**
	 *
	 * @var int
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var float
	 */
	protected $size;
	
	/**
	 *
	 * @var string
	 */
	protected $streamURL;
	
	/**
	 *
	 * @var string
	 */
	protected $fileURL;
	
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var WebexXmlComTrackingType
	 */
	protected $trackingCode;
	
	/**
	 *
	 * @var int
	 */
	protected $recordingType;
	
	/**
	 *
	 * @var long
	 */
	protected $duration;
	
	/**
	 *
	 * @var string
	 */
	protected $author;
	
	/**
	 *
	 * @var WebexXmlComListingType
	 */
	protected $listing;
	
	/**
	 *
	 * @var string
	 */
	protected $format;
	
	/**
	 *
	 * @var WebexXmlComServiceTypeType
	 */
	protected $serviceType;
	
	/**
	 *
	 * @var boolean
	 */
	protected $passwordReq;
	
	/**
	 *
	 * @var boolean
	 */
	protected $registerReq;
	
	/**
	 *
	 * @var string
	 */
	protected $panelist;
	
	/**
	 *
	 * @var boolean
	 */
	protected $postRecordingSurvey;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;

	/**
	 *
	 * @var string
	 */
	protected $password;
	
	/**
	 *
	 * @var string
	 */
	protected $deleteTime;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'recordingID':
				return 'int';
	
			case 'hostWebExID':
				return 'string';
	
			case 'name':
				return 'string';
	
			case 'description':
				return 'string';
	
			case 'createTime':
				return 'string';
	
			case 'timeZoneID':
				return 'int';
	
			case 'size':
				return 'float';
	
			case 'streamURL':
				return 'string';
	
			case 'fileURL':
				return 'string';
	
			case 'sessionKey':
				return 'long';
	
			case 'trackingCode':
				return 'WebexXmlComTrackingType';
	
			case 'recordingType':
				return 'int';
	
			case 'duration':
				return 'long';
	
			case 'author':
				return 'string';
	
			case 'listing':
				return 'WebexXmlComListingType';
	
			case 'format':
				return 'string';
	
			case 'serviceType':
				return 'WebexXmlComServiceTypeType';
	
			case 'passwordReq':
				return 'boolean';
	
			case 'registerReq':
				return 'boolean';
	
			case 'panelist':
				return 'string';
	
			case 'postRecordingSurvey':
				return 'boolean';
	
			case 'confID':
				return 'long';

			case 'password':
				return 'string';
				
			case 'deleteTime':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'recordingID',
			'hostWebExID',
			'name',
			'description',
			'createTime',
			'timeZoneID',
			'size',
			'streamURL',
			'fileURL',
			'sessionKey',
			'trackingCode',
			'recordingType',
			'duration',
			'author',
			'listing',
			'format',
			'serviceType',
			'passwordReq',
			'registerReq',
			'panelist',
			'postRecordingSurvey',
			'confID',
			'password',
			'deleteTime',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'recordingID',
			'hostWebExID',
			'name',
			'description',
			'createTime',
			'timeZoneID',
			'size',
			'streamURL',
			'fileURL',
			'recordingType',
			'duration',
			'format',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'recordingType';
	}
	
	/**
	 * @param int $recordingID
	 */
	public function setRecordingID($recordingID)
	{
		$this->recordingID = $recordingID;
	}
	
	/**
	 * @return int $recordingID
	 */
	public function getRecordingID()
	{
		return $this->recordingID;
	}
	
	/**
	 * @param string $hostWebExID
	 */
	public function setHostWebExID($hostWebExID)
	{
		$this->hostWebExID = $hostWebExID;
	}
	
	/**
	 * @return string $hostWebExID
	 */
	public function getHostWebExID()
	{
		return $this->hostWebExID;
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * @return string $name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return string $description
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * @param string $createTime
	 */
	public function setCreateTime($createTime)
	{
		$this->createTime = $createTime;
	}
	
	/**
	 * @return string $createTime
	 */
	public function getCreateTime()
	{
		return $this->createTime;
	}
	
	/**
	 * @param int $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @return int $timeZoneID
	 */
	public function getTimeZoneID()
	{
		return $this->timeZoneID;
	}
	
	/**
	 * @param float $size
	 */
	public function setSize($size)
	{
		$this->size = $size;
	}
	
	/**
	 * @return float $size
	 */
	public function getSize()
	{
		return $this->size;
	}
	
	/**
	 * @param string $streamURL
	 */
	public function setStreamURL($streamURL)
	{
		$this->streamURL = $streamURL;
	}
	
	/**
	 * @return string $streamURL
	 */
	public function getStreamURL()
	{
		return $this->streamURL;
	}
	
	/**
	 * @param string $fileURL
	 */
	public function setFileURL($fileURL)
	{
		$this->fileURL = $fileURL;
	}
	
	/**
	 * @return string $fileURL
	 */
	public function getFileURL()
	{
		return $this->fileURL;
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @return long $sessionKey
	 */
	public function getSessionKey()
	{
		return $this->sessionKey;
	}
	
	/**
	 * @param WebexXmlComTrackingType $trackingCode
	 */
	public function setTrackingCode(WebexXmlComTrackingType $trackingCode)
	{
		$this->trackingCode = $trackingCode;
	}
	
	/**
	 * @return WebexXmlComTrackingType $trackingCode
	 */
	public function getTrackingCode()
	{
		return $this->trackingCode;
	}
	
	/**
	 * @param int $recordingType
	 */
	public function setRecordingType($recordingType)
	{
		$this->recordingType = $recordingType;
	}
	
	/**
	 * @return int $recordingType
	 */
	public function getRecordingType()
	{
		return $this->recordingType;
	}
	
	/**
	 * @param long $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	/**
	 * @return long $duration
	 */
	public function getDuration()
	{
		return $this->duration;
	}
	
	/**
	 * @param string $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}
	
	/**
	 * @return string $author
	 */
	public function getAuthor()
	{
		return $this->author;
	}
	
	/**
	 * @param WebexXmlComListingType $listing
	 */
	public function setListing(WebexXmlComListingType $listing)
	{
		$this->listing = $listing;
	}
	
	/**
	 * @return WebexXmlComListingType $listing
	 */
	public function getListing()
	{
		return $this->listing;
	}
	
	/**
	 * @param string $format
	 */
	public function setFormat($format)
	{
		$this->format = $format;
	}
	
	/**
	 * @return string $format
	 */
	public function getFormat()
	{
		return $this->format;
	}
	
	/**
	 * @param WebexXmlComServiceTypeType $serviceType
	 */
	public function setServiceType(WebexXmlComServiceTypeType $serviceType)
	{
		$this->serviceType = $serviceType;
	}
	
	/**
	 * @return WebexXmlComServiceTypeType $serviceType
	 */
	public function getServiceType()
	{
		return $this->serviceType;
	}
	
	/**
	 * @param boolean $passwordReq
	 */
	public function setPasswordReq($passwordReq)
	{
		$this->passwordReq = $passwordReq;
	}
	
	/**
	 * @return boolean $passwordReq
	 */
	public function getPasswordReq()
	{
		return $this->passwordReq;
	}
	
	/**
	 * @param boolean $registerReq
	 */
	public function setRegisterReq($registerReq)
	{
		$this->registerReq = $registerReq;
	}
	
	/**
	 * @return boolean $registerReq
	 */
	public function getRegisterReq()
	{
		return $this->registerReq;
	}
	
	/**
	 * @param string $panelist
	 */
	public function setPanelist($panelist)
	{
		$this->panelist = $panelist;
	}
	
	/**
	 * @return string $panelist
	 */
	public function getPanelist()
	{
		return $this->panelist;
	}
	
	/**
	 * @param boolean $postRecordingSurvey
	 */
	public function setPostRecordingSurvey($postRecordingSurvey)
	{
		$this->postRecordingSurvey = $postRecordingSurvey;
	}
	
	/**
	 * @return boolean $postRecordingSurvey
	 */
	public function getPostRecordingSurvey()
	{
		return $this->postRecordingSurvey;
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
	/**
	 * @return long $confID
	 */
	public function getConfID()
	{
		return $this->confID;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * @return string $password
	 */
	public function getPassword()
	{
		return $this->password;
	}
	
	/**
	 * @param string $deleteTime
	 */
	public function setDeleteTime($deleteTime)
	{
		$this->deleteTime = $deleteTime;
	}

	/**
	 * @return string $deleteTime
	 */
	public function getDeleteTime()
	{
		return $this->deleteTime;
	}
	
}
		
