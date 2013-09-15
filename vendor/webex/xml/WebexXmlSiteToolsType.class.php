<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteToolsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $businessDirectory;
	
	/**
	 *
	 * @var boolean
	 */
	protected $officeCalendar;
	
	/**
	 *
	 * @var boolean
	 */
	protected $meetingCalendar;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayOnCallAssistLink;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayProfileLink;
	
	/**
	 *
	 * @var boolean
	 */
	protected $recordingAndPlayback;
	
	/**
	 *
	 * @var boolean
	 */
	protected $recordingEditor;
	
	/**
	 *
	 * @var boolean
	 */
	protected $publishRecordings;
	
	/**
	 *
	 * @var boolean
	 */
	protected $instantMeeting;
	
	/**
	 *
	 * @var boolean
	 */
	protected $emails;
	
	/**
	 *
	 * @var boolean
	 */
	protected $outlookIntegration;
	
	/**
	 *
	 * @var boolean
	 */
	protected $wirelessAccess;
	
	/**
	 *
	 * @var boolean
	 */
	protected $allowPublicAccess;
	
	/**
	 *
	 * @var boolean
	 */
	protected $ssl;
	
	/**
	 *
	 * @var boolean
	 */
	protected $e2e;
	
	/**
	 *
	 * @var boolean
	 */
	protected $handsOnLab;
	
	/**
	 *
	 * @var long
	 */
	protected $holMaxLabs;
	
	/**
	 *
	 * @var long
	 */
	protected $holMaxComputers;
	
	/**
	 *
	 * @var boolean
	 */
	protected $userLockDown;
	
	/**
	 *
	 * @var boolean
	 */
	protected $meetingAssist;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sms;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $encryption;
	
	/**
	 *
	 * @var boolean
	 */
	protected $internalMeeting;
	
	/**
	 *
	 * @var boolean
	 */
	protected $enableTP;
	
	/**
	 *
	 * @var boolean
	 */
	protected $enableTPplus;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'businessDirectory':
				return 'boolean';
	
			case 'officeCalendar':
				return 'boolean';
	
			case 'meetingCalendar':
				return 'boolean';
	
			case 'displayOnCallAssistLink':
				return 'boolean';
	
			case 'displayProfileLink':
				return 'boolean';
	
			case 'recordingAndPlayback':
				return 'boolean';
	
			case 'recordingEditor':
				return 'boolean';
	
			case 'publishRecordings':
				return 'boolean';
	
			case 'instantMeeting':
				return 'boolean';
	
			case 'emails':
				return 'boolean';
	
			case 'outlookIntegration':
				return 'boolean';
	
			case 'wirelessAccess':
				return 'boolean';
	
			case 'allowPublicAccess':
				return 'boolean';
	
			case 'ssl':
				return 'boolean';
	
			case 'e2e':
				return 'boolean';
	
			case 'handsOnLab':
				return 'boolean';
	
			case 'holMaxLabs':
				return 'long';
	
			case 'holMaxComputers':
				return 'long';
	
			case 'userLockDown':
				return 'boolean';
	
			case 'meetingAssist':
				return 'boolean';
	
			case 'sms':
				return 'boolean';
	
			case 'encryption':
				return 'WebexXml';
	
			case 'internalMeeting':
				return 'boolean';
	
			case 'enableTP':
				return 'boolean';
	
			case 'enableTPplus':
				return 'boolean';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'businessDirectory',
			'officeCalendar',
			'meetingCalendar',
			'displayOnCallAssistLink',
			'displayProfileLink',
			'recordingAndPlayback',
			'recordingEditor',
			'publishRecordings',
			'instantMeeting',
			'emails',
			'outlookIntegration',
			'wirelessAccess',
			'allowPublicAccess',
			'ssl',
			'e2e',
			'handsOnLab',
			'holMaxLabs',
			'holMaxComputers',
			'userLockDown',
			'meetingAssist',
			'sms',
			'encryption',
			'internalMeeting',
			'enableTP',
			'enableTPplus',
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
		return 'toolsType';
	}
	
	/**
	 * @param boolean $businessDirectory
	 */
	public function setBusinessDirectory($businessDirectory)
	{
		$this->businessDirectory = $businessDirectory;
	}
	
	/**
	 * @return boolean $businessDirectory
	 */
	public function getBusinessDirectory()
	{
		return $this->businessDirectory;
	}
	
	/**
	 * @param boolean $officeCalendar
	 */
	public function setOfficeCalendar($officeCalendar)
	{
		$this->officeCalendar = $officeCalendar;
	}
	
	/**
	 * @return boolean $officeCalendar
	 */
	public function getOfficeCalendar()
	{
		return $this->officeCalendar;
	}
	
	/**
	 * @param boolean $meetingCalendar
	 */
	public function setMeetingCalendar($meetingCalendar)
	{
		$this->meetingCalendar = $meetingCalendar;
	}
	
	/**
	 * @return boolean $meetingCalendar
	 */
	public function getMeetingCalendar()
	{
		return $this->meetingCalendar;
	}
	
	/**
	 * @param boolean $displayOnCallAssistLink
	 */
	public function setDisplayOnCallAssistLink($displayOnCallAssistLink)
	{
		$this->displayOnCallAssistLink = $displayOnCallAssistLink;
	}
	
	/**
	 * @return boolean $displayOnCallAssistLink
	 */
	public function getDisplayOnCallAssistLink()
	{
		return $this->displayOnCallAssistLink;
	}
	
	/**
	 * @param boolean $displayProfileLink
	 */
	public function setDisplayProfileLink($displayProfileLink)
	{
		$this->displayProfileLink = $displayProfileLink;
	}
	
	/**
	 * @return boolean $displayProfileLink
	 */
	public function getDisplayProfileLink()
	{
		return $this->displayProfileLink;
	}
	
	/**
	 * @param boolean $recordingAndPlayback
	 */
	public function setRecordingAndPlayback($recordingAndPlayback)
	{
		$this->recordingAndPlayback = $recordingAndPlayback;
	}
	
	/**
	 * @return boolean $recordingAndPlayback
	 */
	public function getRecordingAndPlayback()
	{
		return $this->recordingAndPlayback;
	}
	
	/**
	 * @param boolean $recordingEditor
	 */
	public function setRecordingEditor($recordingEditor)
	{
		$this->recordingEditor = $recordingEditor;
	}
	
	/**
	 * @return boolean $recordingEditor
	 */
	public function getRecordingEditor()
	{
		return $this->recordingEditor;
	}
	
	/**
	 * @param boolean $publishRecordings
	 */
	public function setPublishRecordings($publishRecordings)
	{
		$this->publishRecordings = $publishRecordings;
	}
	
	/**
	 * @return boolean $publishRecordings
	 */
	public function getPublishRecordings()
	{
		return $this->publishRecordings;
	}
	
	/**
	 * @param boolean $instantMeeting
	 */
	public function setInstantMeeting($instantMeeting)
	{
		$this->instantMeeting = $instantMeeting;
	}
	
	/**
	 * @return boolean $instantMeeting
	 */
	public function getInstantMeeting()
	{
		return $this->instantMeeting;
	}
	
	/**
	 * @param boolean $emails
	 */
	public function setEmails($emails)
	{
		$this->emails = $emails;
	}
	
	/**
	 * @return boolean $emails
	 */
	public function getEmails()
	{
		return $this->emails;
	}
	
	/**
	 * @param boolean $outlookIntegration
	 */
	public function setOutlookIntegration($outlookIntegration)
	{
		$this->outlookIntegration = $outlookIntegration;
	}
	
	/**
	 * @return boolean $outlookIntegration
	 */
	public function getOutlookIntegration()
	{
		return $this->outlookIntegration;
	}
	
	/**
	 * @param boolean $wirelessAccess
	 */
	public function setWirelessAccess($wirelessAccess)
	{
		$this->wirelessAccess = $wirelessAccess;
	}
	
	/**
	 * @return boolean $wirelessAccess
	 */
	public function getWirelessAccess()
	{
		return $this->wirelessAccess;
	}
	
	/**
	 * @param boolean $allowPublicAccess
	 */
	public function setAllowPublicAccess($allowPublicAccess)
	{
		$this->allowPublicAccess = $allowPublicAccess;
	}
	
	/**
	 * @return boolean $allowPublicAccess
	 */
	public function getAllowPublicAccess()
	{
		return $this->allowPublicAccess;
	}
	
	/**
	 * @param boolean $ssl
	 */
	public function setSsl($ssl)
	{
		$this->ssl = $ssl;
	}
	
	/**
	 * @return boolean $ssl
	 */
	public function getSsl()
	{
		return $this->ssl;
	}
	
	/**
	 * @param boolean $e2e
	 */
	public function setE2e($e2e)
	{
		$this->e2e = $e2e;
	}
	
	/**
	 * @return boolean $e2e
	 */
	public function getE2e()
	{
		return $this->e2e;
	}
	
	/**
	 * @param boolean $handsOnLab
	 */
	public function setHandsOnLab($handsOnLab)
	{
		$this->handsOnLab = $handsOnLab;
	}
	
	/**
	 * @return boolean $handsOnLab
	 */
	public function getHandsOnLab()
	{
		return $this->handsOnLab;
	}
	
	/**
	 * @param long $holMaxLabs
	 */
	public function setHolMaxLabs($holMaxLabs)
	{
		$this->holMaxLabs = $holMaxLabs;
	}
	
	/**
	 * @return long $holMaxLabs
	 */
	public function getHolMaxLabs()
	{
		return $this->holMaxLabs;
	}
	
	/**
	 * @param long $holMaxComputers
	 */
	public function setHolMaxComputers($holMaxComputers)
	{
		$this->holMaxComputers = $holMaxComputers;
	}
	
	/**
	 * @return long $holMaxComputers
	 */
	public function getHolMaxComputers()
	{
		return $this->holMaxComputers;
	}
	
	/**
	 * @param boolean $userLockDown
	 */
	public function setUserLockDown($userLockDown)
	{
		$this->userLockDown = $userLockDown;
	}
	
	/**
	 * @return boolean $userLockDown
	 */
	public function getUserLockDown()
	{
		return $this->userLockDown;
	}
	
	/**
	 * @param boolean $meetingAssist
	 */
	public function setMeetingAssist($meetingAssist)
	{
		$this->meetingAssist = $meetingAssist;
	}
	
	/**
	 * @return boolean $meetingAssist
	 */
	public function getMeetingAssist()
	{
		return $this->meetingAssist;
	}
	
	/**
	 * @param boolean $sms
	 */
	public function setSms($sms)
	{
		$this->sms = $sms;
	}
	
	/**
	 * @return boolean $sms
	 */
	public function getSms()
	{
		return $this->sms;
	}
	
	/**
	 * @param WebexXml $encryption
	 */
	public function setEncryption(WebexXml $encryption)
	{
		$this->encryption = $encryption;
	}
	
	/**
	 * @return WebexXml $encryption
	 */
	public function getEncryption()
	{
		return $this->encryption;
	}
	
	/**
	 * @param boolean $internalMeeting
	 */
	public function setInternalMeeting($internalMeeting)
	{
		$this->internalMeeting = $internalMeeting;
	}
	
	/**
	 * @return boolean $internalMeeting
	 */
	public function getInternalMeeting()
	{
		return $this->internalMeeting;
	}
	
	/**
	 * @param boolean $enableTP
	 */
	public function setEnableTP($enableTP)
	{
		$this->enableTP = $enableTP;
	}
	
	/**
	 * @return boolean $enableTP
	 */
	public function getEnableTP()
	{
		return $this->enableTP;
	}
	
	/**
	 * @param boolean $enableTPplus
	 */
	public function setEnableTPplus($enableTPplus)
	{
		$this->enableTPplus = $enableTPplus;
	}
	
	/**
	 * @return boolean $enableTPplus
	 */
	public function getEnableTPplus()
	{
		return $this->enableTPplus;
	}
	
}
		
