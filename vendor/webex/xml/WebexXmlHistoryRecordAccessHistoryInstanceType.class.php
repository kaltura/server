<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistoryRecordAccessHistoryInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $recordID;
	
	/**
	 *
	 * @var string
	 */
	protected $recordName;
	
	/**
	 *
	 * @var string
	 */
	protected $creationTime;
	
	/**
	 *
	 * @var long
	 */
	protected $registered;
	
	/**
	 *
	 * @var long
	 */
	protected $downloaded;
	
	/**
	 *
	 * @var long
	 */
	protected $viewed;
	
	/**
	 *
	 * @var int
	 */
	protected $timeZoneID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'recordID':
				return 'long';
	
			case 'recordName':
				return 'string';
	
			case 'creationTime':
				return 'string';
	
			case 'registered':
				return 'long';
	
			case 'downloaded':
				return 'long';
	
			case 'viewed':
				return 'long';
	
			case 'timeZoneID':
				return 'int';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'recordID',
			'recordName',
			'creationTime',
			'registered',
			'downloaded',
			'viewed',
			'timeZoneID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'recordAccessHistoryInstanceType';
	}
	
	/**
	 * @param long $recordID
	 */
	public function setRecordID($recordID)
	{
		$this->recordID = $recordID;
	}
	
	/**
	 * @return long $recordID
	 */
	public function getRecordID()
	{
		return $this->recordID;
	}
	
	/**
	 * @param string $recordName
	 */
	public function setRecordName($recordName)
	{
		$this->recordName = $recordName;
	}
	
	/**
	 * @return string $recordName
	 */
	public function getRecordName()
	{
		return $this->recordName;
	}
	
	/**
	 * @param string $creationTime
	 */
	public function setCreationTime($creationTime)
	{
		$this->creationTime = $creationTime;
	}
	
	/**
	 * @return string $creationTime
	 */
	public function getCreationTime()
	{
		return $this->creationTime;
	}
	
	/**
	 * @param long $registered
	 */
	public function setRegistered($registered)
	{
		$this->registered = $registered;
	}
	
	/**
	 * @return long $registered
	 */
	public function getRegistered()
	{
		return $this->registered;
	}
	
	/**
	 * @param long $downloaded
	 */
	public function setDownloaded($downloaded)
	{
		$this->downloaded = $downloaded;
	}
	
	/**
	 * @return long $downloaded
	 */
	public function getDownloaded()
	{
		return $this->downloaded;
	}
	
	/**
	 * @param long $viewed
	 */
	public function setViewed($viewed)
	{
		$this->viewed = $viewed;
	}
	
	/**
	 * @return long $viewed
	 */
	public function getViewed()
	{
		return $this->viewed;
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
	
}

