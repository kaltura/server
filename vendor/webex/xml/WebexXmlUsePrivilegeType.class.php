<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUsePrivilegeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $host;
	
	/**
	 *
	 * @var boolean
	 */
	protected $teleConfCallOut;
	
	/**
	 *
	 * @var boolean
	 */
	protected $teleConfCallOutInternational;
	
	/**
	 *
	 * @var boolean
	 */
	protected $teleConfCallIn;
	
	/**
	 *
	 * @var boolean
	 */
	protected $teleConfTollFreeCallIn;
	
	/**
	 *
	 * @var boolean
	 */
	protected $siteAdmin;
	
	/**
	 *
	 * @var boolean
	 */
	protected $voiceOverIp;
	
	/**
	 *
	 * @var boolean
	 */
	protected $roSiteAdmin;
	
	/**
	 *
	 * @var boolean
	 */
	protected $labAdmin;
	
	/**
	 *
	 * @var boolean
	 */
	protected $otherTelephony;
	
	/**
	 *
	 * @var boolean
	 */
	protected $teleConfCallInInternational;
	
	/**
	 *
	 * @var boolean
	 */
	protected $attendeeOnly;
	
	/**
	 *
	 * @var boolean
	 */
	protected $recordingEditor;
	
	/**
	 *
	 * @var boolean
	 */
	protected $meetingAssist;
	
	/**
	 *
	 * @var boolean
	 */
	protected $HQvideo;
	
	/**
	 *
	 * @var boolean
	 */
	protected $allowExtAttendees;
	
	/**
	 *
	 * @var boolean
	 */
	protected $HDvideo;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'host':
				return 'boolean';
	
			case 'teleConfCallOut':
				return 'boolean';
	
			case 'teleConfCallOutInternational':
				return 'boolean';
	
			case 'teleConfCallIn':
				return 'boolean';
	
			case 'teleConfTollFreeCallIn':
				return 'boolean';
	
			case 'siteAdmin':
				return 'boolean';
	
			case 'voiceOverIp':
				return 'boolean';
	
			case 'roSiteAdmin':
				return 'boolean';
	
			case 'labAdmin':
				return 'boolean';
	
			case 'otherTelephony':
				return 'boolean';
	
			case 'teleConfCallInInternational':
				return 'boolean';
	
			case 'attendeeOnly':
				return 'boolean';
	
			case 'recordingEditor':
				return 'boolean';
	
			case 'meetingAssist':
				return 'boolean';
	
			case 'HQvideo':
				return 'boolean';
	
			case 'allowExtAttendees':
				return 'boolean';
	
			case 'HDvideo':
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
			'host',
			'teleConfCallOut',
			'teleConfCallOutInternational',
			'teleConfCallIn',
			'teleConfTollFreeCallIn',
			'siteAdmin',
			'voiceOverIp',
			'roSiteAdmin',
			'labAdmin',
			'otherTelephony',
			'teleConfCallInInternational',
			'attendeeOnly',
			'recordingEditor',
			'meetingAssist',
			'HQvideo',
			'allowExtAttendees',
			'HDvideo',
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
		return 'privilegeType';
	}
	
	/**
	 * @param boolean $host
	 */
	public function setHost($host)
	{
		$this->host = $host;
	}
	
	/**
	 * @return boolean $host
	 */
	public function getHost()
	{
		return $this->host;
	}
	
	/**
	 * @param boolean $teleConfCallOut
	 */
	public function setTeleConfCallOut($teleConfCallOut)
	{
		$this->teleConfCallOut = $teleConfCallOut;
	}
	
	/**
	 * @return boolean $teleConfCallOut
	 */
	public function getTeleConfCallOut()
	{
		return $this->teleConfCallOut;
	}
	
	/**
	 * @param boolean $teleConfCallOutInternational
	 */
	public function setTeleConfCallOutInternational($teleConfCallOutInternational)
	{
		$this->teleConfCallOutInternational = $teleConfCallOutInternational;
	}
	
	/**
	 * @return boolean $teleConfCallOutInternational
	 */
	public function getTeleConfCallOutInternational()
	{
		return $this->teleConfCallOutInternational;
	}
	
	/**
	 * @param boolean $teleConfCallIn
	 */
	public function setTeleConfCallIn($teleConfCallIn)
	{
		$this->teleConfCallIn = $teleConfCallIn;
	}
	
	/**
	 * @return boolean $teleConfCallIn
	 */
	public function getTeleConfCallIn()
	{
		return $this->teleConfCallIn;
	}
	
	/**
	 * @param boolean $teleConfTollFreeCallIn
	 */
	public function setTeleConfTollFreeCallIn($teleConfTollFreeCallIn)
	{
		$this->teleConfTollFreeCallIn = $teleConfTollFreeCallIn;
	}
	
	/**
	 * @return boolean $teleConfTollFreeCallIn
	 */
	public function getTeleConfTollFreeCallIn()
	{
		return $this->teleConfTollFreeCallIn;
	}
	
	/**
	 * @param boolean $siteAdmin
	 */
	public function setSiteAdmin($siteAdmin)
	{
		$this->siteAdmin = $siteAdmin;
	}
	
	/**
	 * @return boolean $siteAdmin
	 */
	public function getSiteAdmin()
	{
		return $this->siteAdmin;
	}
	
	/**
	 * @param boolean $voiceOverIp
	 */
	public function setVoiceOverIp($voiceOverIp)
	{
		$this->voiceOverIp = $voiceOverIp;
	}
	
	/**
	 * @return boolean $voiceOverIp
	 */
	public function getVoiceOverIp()
	{
		return $this->voiceOverIp;
	}
	
	/**
	 * @param boolean $roSiteAdmin
	 */
	public function setRoSiteAdmin($roSiteAdmin)
	{
		$this->roSiteAdmin = $roSiteAdmin;
	}
	
	/**
	 * @return boolean $roSiteAdmin
	 */
	public function getRoSiteAdmin()
	{
		return $this->roSiteAdmin;
	}
	
	/**
	 * @param boolean $labAdmin
	 */
	public function setLabAdmin($labAdmin)
	{
		$this->labAdmin = $labAdmin;
	}
	
	/**
	 * @return boolean $labAdmin
	 */
	public function getLabAdmin()
	{
		return $this->labAdmin;
	}
	
	/**
	 * @param boolean $otherTelephony
	 */
	public function setOtherTelephony($otherTelephony)
	{
		$this->otherTelephony = $otherTelephony;
	}
	
	/**
	 * @return boolean $otherTelephony
	 */
	public function getOtherTelephony()
	{
		return $this->otherTelephony;
	}
	
	/**
	 * @param boolean $teleConfCallInInternational
	 */
	public function setTeleConfCallInInternational($teleConfCallInInternational)
	{
		$this->teleConfCallInInternational = $teleConfCallInInternational;
	}
	
	/**
	 * @return boolean $teleConfCallInInternational
	 */
	public function getTeleConfCallInInternational()
	{
		return $this->teleConfCallInInternational;
	}
	
	/**
	 * @param boolean $attendeeOnly
	 */
	public function setAttendeeOnly($attendeeOnly)
	{
		$this->attendeeOnly = $attendeeOnly;
	}
	
	/**
	 * @return boolean $attendeeOnly
	 */
	public function getAttendeeOnly()
	{
		return $this->attendeeOnly;
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
	 * @param boolean $HQvideo
	 */
	public function setHQvideo($HQvideo)
	{
		$this->HQvideo = $HQvideo;
	}
	
	/**
	 * @return boolean $HQvideo
	 */
	public function getHQvideo()
	{
		return $this->HQvideo;
	}
	
	/**
	 * @param boolean $allowExtAttendees
	 */
	public function setAllowExtAttendees($allowExtAttendees)
	{
		$this->allowExtAttendees = $allowExtAttendees;
	}
	
	/**
	 * @return boolean $allowExtAttendees
	 */
	public function getAllowExtAttendees()
	{
		return $this->allowExtAttendees;
	}
	
	/**
	 * @param boolean $HDvideo
	 */
	public function setHDvideo($HDvideo)
	{
		$this->HDvideo = $HDvideo;
	}
	
	/**
	 * @return boolean $HDvideo
	 */
	public function getHDvideo()
	{
		return $this->HDvideo;
	}
	
}
		
