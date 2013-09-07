<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingtypeOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $supportAppShare;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportAppShareRemote;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportAttendeeRegistration;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportRemoteWebTour;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportWebTour;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportFileShare;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportChat;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportCobrowseSite;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportCorporateOfficesSite;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportDesktopShare;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportDesktopShareRemote;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportFileTransfer;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportInternationalCallOut;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportJavaClient;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportMacClient;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportMeetingCenterSite;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportMeetingRecord;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportMultipleMeeting;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportOnCallSite;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportOnStageSite;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportPartnerOfficesSite;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportPoll;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportPresentation;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportSolarisClient;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportSSL;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportE2E;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportPKI;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportTeleconfCallIn;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportTeleconfCallOut;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportTollFreeCallIn;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportVideo;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportVoIP;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportWebExComSite;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportWindowsClient;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportQuickStartAttendees;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportQuickStartHost;
	
	/**
	 *
	 * @var boolean
	 */
	protected $hideInScheduler;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'supportAppShare':
				return 'boolean';
	
			case 'supportAppShareRemote':
				return 'boolean';
	
			case 'supportAttendeeRegistration':
				return 'boolean';
	
			case 'supportRemoteWebTour':
				return 'boolean';
	
			case 'supportWebTour':
				return 'boolean';
	
			case 'supportFileShare':
				return 'boolean';
	
			case 'supportChat':
				return 'boolean';
	
			case 'supportCobrowseSite':
				return 'boolean';
	
			case 'supportCorporateOfficesSite':
				return 'boolean';
	
			case 'supportDesktopShare':
				return 'boolean';
	
			case 'supportDesktopShareRemote':
				return 'boolean';
	
			case 'supportFileTransfer':
				return 'boolean';
	
			case 'supportInternationalCallOut':
				return 'boolean';
	
			case 'supportJavaClient':
				return 'boolean';
	
			case 'supportMacClient':
				return 'boolean';
	
			case 'supportMeetingCenterSite':
				return 'boolean';
	
			case 'supportMeetingRecord':
				return 'boolean';
	
			case 'supportMultipleMeeting':
				return 'boolean';
	
			case 'supportOnCallSite':
				return 'boolean';
	
			case 'supportOnStageSite':
				return 'boolean';
	
			case 'supportPartnerOfficesSite':
				return 'boolean';
	
			case 'supportPoll':
				return 'boolean';
	
			case 'supportPresentation':
				return 'boolean';
	
			case 'supportSolarisClient':
				return 'boolean';
	
			case 'supportSSL':
				return 'boolean';
	
			case 'supportE2E':
				return 'boolean';
	
			case 'supportPKI':
				return 'boolean';
	
			case 'supportTeleconfCallIn':
				return 'boolean';
	
			case 'supportTeleconfCallOut':
				return 'boolean';
	
			case 'supportTollFreeCallIn':
				return 'boolean';
	
			case 'supportVideo':
				return 'boolean';
	
			case 'supportVoIP':
				return 'boolean';
	
			case 'supportWebExComSite':
				return 'boolean';
	
			case 'supportWindowsClient':
				return 'boolean';
	
			case 'supportQuickStartAttendees':
				return 'boolean';
	
			case 'supportQuickStartHost':
				return 'boolean';
	
			case 'hideInScheduler':
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
			'supportAppShare',
			'supportAppShareRemote',
			'supportAttendeeRegistration',
			'supportRemoteWebTour',
			'supportWebTour',
			'supportFileShare',
			'supportChat',
			'supportCobrowseSite',
			'supportCorporateOfficesSite',
			'supportDesktopShare',
			'supportDesktopShareRemote',
			'supportFileTransfer',
			'supportInternationalCallOut',
			'supportJavaClient',
			'supportMacClient',
			'supportMeetingCenterSite',
			'supportMeetingRecord',
			'supportMultipleMeeting',
			'supportOnCallSite',
			'supportOnStageSite',
			'supportPartnerOfficesSite',
			'supportPoll',
			'supportPresentation',
			'supportSolarisClient',
			'supportSSL',
			'supportE2E',
			'supportPKI',
			'supportTeleconfCallIn',
			'supportTeleconfCallOut',
			'supportTollFreeCallIn',
			'supportVideo',
			'supportVoIP',
			'supportWebExComSite',
			'supportWindowsClient',
			'supportQuickStartAttendees',
			'supportQuickStartHost',
			'hideInScheduler',
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
		return 'optionsType';
	}
	
	/**
	 * @param boolean $supportAppShare
	 */
	public function setSupportAppShare($supportAppShare)
	{
		$this->supportAppShare = $supportAppShare;
	}
	
	/**
	 * @return boolean $supportAppShare
	 */
	public function getSupportAppShare()
	{
		return $this->supportAppShare;
	}
	
	/**
	 * @param boolean $supportAppShareRemote
	 */
	public function setSupportAppShareRemote($supportAppShareRemote)
	{
		$this->supportAppShareRemote = $supportAppShareRemote;
	}
	
	/**
	 * @return boolean $supportAppShareRemote
	 */
	public function getSupportAppShareRemote()
	{
		return $this->supportAppShareRemote;
	}
	
	/**
	 * @param boolean $supportAttendeeRegistration
	 */
	public function setSupportAttendeeRegistration($supportAttendeeRegistration)
	{
		$this->supportAttendeeRegistration = $supportAttendeeRegistration;
	}
	
	/**
	 * @return boolean $supportAttendeeRegistration
	 */
	public function getSupportAttendeeRegistration()
	{
		return $this->supportAttendeeRegistration;
	}
	
	/**
	 * @param boolean $supportRemoteWebTour
	 */
	public function setSupportRemoteWebTour($supportRemoteWebTour)
	{
		$this->supportRemoteWebTour = $supportRemoteWebTour;
	}
	
	/**
	 * @return boolean $supportRemoteWebTour
	 */
	public function getSupportRemoteWebTour()
	{
		return $this->supportRemoteWebTour;
	}
	
	/**
	 * @param boolean $supportWebTour
	 */
	public function setSupportWebTour($supportWebTour)
	{
		$this->supportWebTour = $supportWebTour;
	}
	
	/**
	 * @return boolean $supportWebTour
	 */
	public function getSupportWebTour()
	{
		return $this->supportWebTour;
	}
	
	/**
	 * @param boolean $supportFileShare
	 */
	public function setSupportFileShare($supportFileShare)
	{
		$this->supportFileShare = $supportFileShare;
	}
	
	/**
	 * @return boolean $supportFileShare
	 */
	public function getSupportFileShare()
	{
		return $this->supportFileShare;
	}
	
	/**
	 * @param boolean $supportChat
	 */
	public function setSupportChat($supportChat)
	{
		$this->supportChat = $supportChat;
	}
	
	/**
	 * @return boolean $supportChat
	 */
	public function getSupportChat()
	{
		return $this->supportChat;
	}
	
	/**
	 * @param boolean $supportCobrowseSite
	 */
	public function setSupportCobrowseSite($supportCobrowseSite)
	{
		$this->supportCobrowseSite = $supportCobrowseSite;
	}
	
	/**
	 * @return boolean $supportCobrowseSite
	 */
	public function getSupportCobrowseSite()
	{
		return $this->supportCobrowseSite;
	}
	
	/**
	 * @param boolean $supportCorporateOfficesSite
	 */
	public function setSupportCorporateOfficesSite($supportCorporateOfficesSite)
	{
		$this->supportCorporateOfficesSite = $supportCorporateOfficesSite;
	}
	
	/**
	 * @return boolean $supportCorporateOfficesSite
	 */
	public function getSupportCorporateOfficesSite()
	{
		return $this->supportCorporateOfficesSite;
	}
	
	/**
	 * @param boolean $supportDesktopShare
	 */
	public function setSupportDesktopShare($supportDesktopShare)
	{
		$this->supportDesktopShare = $supportDesktopShare;
	}
	
	/**
	 * @return boolean $supportDesktopShare
	 */
	public function getSupportDesktopShare()
	{
		return $this->supportDesktopShare;
	}
	
	/**
	 * @param boolean $supportDesktopShareRemote
	 */
	public function setSupportDesktopShareRemote($supportDesktopShareRemote)
	{
		$this->supportDesktopShareRemote = $supportDesktopShareRemote;
	}
	
	/**
	 * @return boolean $supportDesktopShareRemote
	 */
	public function getSupportDesktopShareRemote()
	{
		return $this->supportDesktopShareRemote;
	}
	
	/**
	 * @param boolean $supportFileTransfer
	 */
	public function setSupportFileTransfer($supportFileTransfer)
	{
		$this->supportFileTransfer = $supportFileTransfer;
	}
	
	/**
	 * @return boolean $supportFileTransfer
	 */
	public function getSupportFileTransfer()
	{
		return $this->supportFileTransfer;
	}
	
	/**
	 * @param boolean $supportInternationalCallOut
	 */
	public function setSupportInternationalCallOut($supportInternationalCallOut)
	{
		$this->supportInternationalCallOut = $supportInternationalCallOut;
	}
	
	/**
	 * @return boolean $supportInternationalCallOut
	 */
	public function getSupportInternationalCallOut()
	{
		return $this->supportInternationalCallOut;
	}
	
	/**
	 * @param boolean $supportJavaClient
	 */
	public function setSupportJavaClient($supportJavaClient)
	{
		$this->supportJavaClient = $supportJavaClient;
	}
	
	/**
	 * @return boolean $supportJavaClient
	 */
	public function getSupportJavaClient()
	{
		return $this->supportJavaClient;
	}
	
	/**
	 * @param boolean $supportMacClient
	 */
	public function setSupportMacClient($supportMacClient)
	{
		$this->supportMacClient = $supportMacClient;
	}
	
	/**
	 * @return boolean $supportMacClient
	 */
	public function getSupportMacClient()
	{
		return $this->supportMacClient;
	}
	
	/**
	 * @param boolean $supportMeetingCenterSite
	 */
	public function setSupportMeetingCenterSite($supportMeetingCenterSite)
	{
		$this->supportMeetingCenterSite = $supportMeetingCenterSite;
	}
	
	/**
	 * @return boolean $supportMeetingCenterSite
	 */
	public function getSupportMeetingCenterSite()
	{
		return $this->supportMeetingCenterSite;
	}
	
	/**
	 * @param boolean $supportMeetingRecord
	 */
	public function setSupportMeetingRecord($supportMeetingRecord)
	{
		$this->supportMeetingRecord = $supportMeetingRecord;
	}
	
	/**
	 * @return boolean $supportMeetingRecord
	 */
	public function getSupportMeetingRecord()
	{
		return $this->supportMeetingRecord;
	}
	
	/**
	 * @param boolean $supportMultipleMeeting
	 */
	public function setSupportMultipleMeeting($supportMultipleMeeting)
	{
		$this->supportMultipleMeeting = $supportMultipleMeeting;
	}
	
	/**
	 * @return boolean $supportMultipleMeeting
	 */
	public function getSupportMultipleMeeting()
	{
		return $this->supportMultipleMeeting;
	}
	
	/**
	 * @param boolean $supportOnCallSite
	 */
	public function setSupportOnCallSite($supportOnCallSite)
	{
		$this->supportOnCallSite = $supportOnCallSite;
	}
	
	/**
	 * @return boolean $supportOnCallSite
	 */
	public function getSupportOnCallSite()
	{
		return $this->supportOnCallSite;
	}
	
	/**
	 * @param boolean $supportOnStageSite
	 */
	public function setSupportOnStageSite($supportOnStageSite)
	{
		$this->supportOnStageSite = $supportOnStageSite;
	}
	
	/**
	 * @return boolean $supportOnStageSite
	 */
	public function getSupportOnStageSite()
	{
		return $this->supportOnStageSite;
	}
	
	/**
	 * @param boolean $supportPartnerOfficesSite
	 */
	public function setSupportPartnerOfficesSite($supportPartnerOfficesSite)
	{
		$this->supportPartnerOfficesSite = $supportPartnerOfficesSite;
	}
	
	/**
	 * @return boolean $supportPartnerOfficesSite
	 */
	public function getSupportPartnerOfficesSite()
	{
		return $this->supportPartnerOfficesSite;
	}
	
	/**
	 * @param boolean $supportPoll
	 */
	public function setSupportPoll($supportPoll)
	{
		$this->supportPoll = $supportPoll;
	}
	
	/**
	 * @return boolean $supportPoll
	 */
	public function getSupportPoll()
	{
		return $this->supportPoll;
	}
	
	/**
	 * @param boolean $supportPresentation
	 */
	public function setSupportPresentation($supportPresentation)
	{
		$this->supportPresentation = $supportPresentation;
	}
	
	/**
	 * @return boolean $supportPresentation
	 */
	public function getSupportPresentation()
	{
		return $this->supportPresentation;
	}
	
	/**
	 * @param boolean $supportSolarisClient
	 */
	public function setSupportSolarisClient($supportSolarisClient)
	{
		$this->supportSolarisClient = $supportSolarisClient;
	}
	
	/**
	 * @return boolean $supportSolarisClient
	 */
	public function getSupportSolarisClient()
	{
		return $this->supportSolarisClient;
	}
	
	/**
	 * @param boolean $supportSSL
	 */
	public function setSupportSSL($supportSSL)
	{
		$this->supportSSL = $supportSSL;
	}
	
	/**
	 * @return boolean $supportSSL
	 */
	public function getSupportSSL()
	{
		return $this->supportSSL;
	}
	
	/**
	 * @param boolean $supportE2E
	 */
	public function setSupportE2E($supportE2E)
	{
		$this->supportE2E = $supportE2E;
	}
	
	/**
	 * @return boolean $supportE2E
	 */
	public function getSupportE2E()
	{
		return $this->supportE2E;
	}
	
	/**
	 * @param boolean $supportPKI
	 */
	public function setSupportPKI($supportPKI)
	{
		$this->supportPKI = $supportPKI;
	}
	
	/**
	 * @return boolean $supportPKI
	 */
	public function getSupportPKI()
	{
		return $this->supportPKI;
	}
	
	/**
	 * @param boolean $supportTeleconfCallIn
	 */
	public function setSupportTeleconfCallIn($supportTeleconfCallIn)
	{
		$this->supportTeleconfCallIn = $supportTeleconfCallIn;
	}
	
	/**
	 * @return boolean $supportTeleconfCallIn
	 */
	public function getSupportTeleconfCallIn()
	{
		return $this->supportTeleconfCallIn;
	}
	
	/**
	 * @param boolean $supportTeleconfCallOut
	 */
	public function setSupportTeleconfCallOut($supportTeleconfCallOut)
	{
		$this->supportTeleconfCallOut = $supportTeleconfCallOut;
	}
	
	/**
	 * @return boolean $supportTeleconfCallOut
	 */
	public function getSupportTeleconfCallOut()
	{
		return $this->supportTeleconfCallOut;
	}
	
	/**
	 * @param boolean $supportTollFreeCallIn
	 */
	public function setSupportTollFreeCallIn($supportTollFreeCallIn)
	{
		$this->supportTollFreeCallIn = $supportTollFreeCallIn;
	}
	
	/**
	 * @return boolean $supportTollFreeCallIn
	 */
	public function getSupportTollFreeCallIn()
	{
		return $this->supportTollFreeCallIn;
	}
	
	/**
	 * @param boolean $supportVideo
	 */
	public function setSupportVideo($supportVideo)
	{
		$this->supportVideo = $supportVideo;
	}
	
	/**
	 * @return boolean $supportVideo
	 */
	public function getSupportVideo()
	{
		return $this->supportVideo;
	}
	
	/**
	 * @param boolean $supportVoIP
	 */
	public function setSupportVoIP($supportVoIP)
	{
		$this->supportVoIP = $supportVoIP;
	}
	
	/**
	 * @return boolean $supportVoIP
	 */
	public function getSupportVoIP()
	{
		return $this->supportVoIP;
	}
	
	/**
	 * @param boolean $supportWebExComSite
	 */
	public function setSupportWebExComSite($supportWebExComSite)
	{
		$this->supportWebExComSite = $supportWebExComSite;
	}
	
	/**
	 * @return boolean $supportWebExComSite
	 */
	public function getSupportWebExComSite()
	{
		return $this->supportWebExComSite;
	}
	
	/**
	 * @param boolean $supportWindowsClient
	 */
	public function setSupportWindowsClient($supportWindowsClient)
	{
		$this->supportWindowsClient = $supportWindowsClient;
	}
	
	/**
	 * @return boolean $supportWindowsClient
	 */
	public function getSupportWindowsClient()
	{
		return $this->supportWindowsClient;
	}
	
	/**
	 * @param boolean $supportQuickStartAttendees
	 */
	public function setSupportQuickStartAttendees($supportQuickStartAttendees)
	{
		$this->supportQuickStartAttendees = $supportQuickStartAttendees;
	}
	
	/**
	 * @return boolean $supportQuickStartAttendees
	 */
	public function getSupportQuickStartAttendees()
	{
		return $this->supportQuickStartAttendees;
	}
	
	/**
	 * @param boolean $supportQuickStartHost
	 */
	public function setSupportQuickStartHost($supportQuickStartHost)
	{
		$this->supportQuickStartHost = $supportQuickStartHost;
	}
	
	/**
	 * @return boolean $supportQuickStartHost
	 */
	public function getSupportQuickStartHost()
	{
		return $this->supportQuickStartHost;
	}
	
	/**
	 * @param boolean $hideInScheduler
	 */
	public function setHideInScheduler($hideInScheduler)
	{
		$this->hideInScheduler = $hideInScheduler;
	}
	
	/**
	 * @return boolean $hideInScheduler
	 */
	public function getHideInScheduler()
	{
		return $this->hideInScheduler;
	}
	
}
		
