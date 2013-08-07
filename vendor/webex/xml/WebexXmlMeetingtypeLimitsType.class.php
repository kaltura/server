<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingtypeLimitsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $maxAppShareDuration;
	
	/**
	 *
	 * @var long
	 */
	protected $maxAppShareUser;
	
	/**
	 *
	 * @var long
	 */
	protected $maxDesktopShareDuration;
	
	/**
	 *
	 * @var long
	 */
	protected $maxDesktopShareUser;
	
	/**
	 *
	 * @var long
	 */
	protected $maxFileTransferUser;
	
	/**
	 *
	 * @var long
	 */
	protected $maxMeetingDuration;
	
	/**
	 *
	 * @var long
	 */
	protected $maxMeetingUser;
	
	/**
	 *
	 * @var long
	 */
	protected $maxRecordUser;
	
	/**
	 *
	 * @var long
	 */
	protected $maxVideoDuration;
	
	/**
	 *
	 * @var long
	 */
	protected $maxVideoUser;
	
	/**
	 *
	 * @var long
	 */
	protected $maxWebTourDuration;
	
	/**
	 *
	 * @var long
	 */
	protected $maxWebTourUser;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'maxAppShareDuration':
				return 'long';
	
			case 'maxAppShareUser':
				return 'long';
	
			case 'maxDesktopShareDuration':
				return 'long';
	
			case 'maxDesktopShareUser':
				return 'long';
	
			case 'maxFileTransferUser':
				return 'long';
	
			case 'maxMeetingDuration':
				return 'long';
	
			case 'maxMeetingUser':
				return 'long';
	
			case 'maxRecordUser':
				return 'long';
	
			case 'maxVideoDuration':
				return 'long';
	
			case 'maxVideoUser':
				return 'long';
	
			case 'maxWebTourDuration':
				return 'long';
	
			case 'maxWebTourUser':
				return 'long';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'maxAppShareDuration',
			'maxAppShareUser',
			'maxDesktopShareDuration',
			'maxDesktopShareUser',
			'maxFileTransferUser',
			'maxMeetingDuration',
			'maxMeetingUser',
			'maxRecordUser',
			'maxVideoDuration',
			'maxVideoUser',
			'maxWebTourDuration',
			'maxWebTourUser',
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
		return 'limitsType';
	}
	
	/**
	 * @param long $maxAppShareDuration
	 */
	public function setMaxAppShareDuration($maxAppShareDuration)
	{
		$this->maxAppShareDuration = $maxAppShareDuration;
	}
	
	/**
	 * @return long $maxAppShareDuration
	 */
	public function getMaxAppShareDuration()
	{
		return $this->maxAppShareDuration;
	}
	
	/**
	 * @param long $maxAppShareUser
	 */
	public function setMaxAppShareUser($maxAppShareUser)
	{
		$this->maxAppShareUser = $maxAppShareUser;
	}
	
	/**
	 * @return long $maxAppShareUser
	 */
	public function getMaxAppShareUser()
	{
		return $this->maxAppShareUser;
	}
	
	/**
	 * @param long $maxDesktopShareDuration
	 */
	public function setMaxDesktopShareDuration($maxDesktopShareDuration)
	{
		$this->maxDesktopShareDuration = $maxDesktopShareDuration;
	}
	
	/**
	 * @return long $maxDesktopShareDuration
	 */
	public function getMaxDesktopShareDuration()
	{
		return $this->maxDesktopShareDuration;
	}
	
	/**
	 * @param long $maxDesktopShareUser
	 */
	public function setMaxDesktopShareUser($maxDesktopShareUser)
	{
		$this->maxDesktopShareUser = $maxDesktopShareUser;
	}
	
	/**
	 * @return long $maxDesktopShareUser
	 */
	public function getMaxDesktopShareUser()
	{
		return $this->maxDesktopShareUser;
	}
	
	/**
	 * @param long $maxFileTransferUser
	 */
	public function setMaxFileTransferUser($maxFileTransferUser)
	{
		$this->maxFileTransferUser = $maxFileTransferUser;
	}
	
	/**
	 * @return long $maxFileTransferUser
	 */
	public function getMaxFileTransferUser()
	{
		return $this->maxFileTransferUser;
	}
	
	/**
	 * @param long $maxMeetingDuration
	 */
	public function setMaxMeetingDuration($maxMeetingDuration)
	{
		$this->maxMeetingDuration = $maxMeetingDuration;
	}
	
	/**
	 * @return long $maxMeetingDuration
	 */
	public function getMaxMeetingDuration()
	{
		return $this->maxMeetingDuration;
	}
	
	/**
	 * @param long $maxMeetingUser
	 */
	public function setMaxMeetingUser($maxMeetingUser)
	{
		$this->maxMeetingUser = $maxMeetingUser;
	}
	
	/**
	 * @return long $maxMeetingUser
	 */
	public function getMaxMeetingUser()
	{
		return $this->maxMeetingUser;
	}
	
	/**
	 * @param long $maxRecordUser
	 */
	public function setMaxRecordUser($maxRecordUser)
	{
		$this->maxRecordUser = $maxRecordUser;
	}
	
	/**
	 * @return long $maxRecordUser
	 */
	public function getMaxRecordUser()
	{
		return $this->maxRecordUser;
	}
	
	/**
	 * @param long $maxVideoDuration
	 */
	public function setMaxVideoDuration($maxVideoDuration)
	{
		$this->maxVideoDuration = $maxVideoDuration;
	}
	
	/**
	 * @return long $maxVideoDuration
	 */
	public function getMaxVideoDuration()
	{
		return $this->maxVideoDuration;
	}
	
	/**
	 * @param long $maxVideoUser
	 */
	public function setMaxVideoUser($maxVideoUser)
	{
		$this->maxVideoUser = $maxVideoUser;
	}
	
	/**
	 * @return long $maxVideoUser
	 */
	public function getMaxVideoUser()
	{
		return $this->maxVideoUser;
	}
	
	/**
	 * @param long $maxWebTourDuration
	 */
	public function setMaxWebTourDuration($maxWebTourDuration)
	{
		$this->maxWebTourDuration = $maxWebTourDuration;
	}
	
	/**
	 * @return long $maxWebTourDuration
	 */
	public function getMaxWebTourDuration()
	{
		return $this->maxWebTourDuration;
	}
	
	/**
	 * @param long $maxWebTourUser
	 */
	public function setMaxWebTourUser($maxWebTourUser)
	{
		$this->maxWebTourUser = $maxWebTourUser;
	}
	
	/**
	 * @return long $maxWebTourUser
	 */
	public function getMaxWebTourUser()
	{
		return $this->maxWebTourUser;
	}
	
}
		
