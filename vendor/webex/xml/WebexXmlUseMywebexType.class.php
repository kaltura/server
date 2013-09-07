<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseMywebexType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $isMyWebExPro;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myContact;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myProfile;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myMeetings;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myFolders;
	
	/**
	 *
	 * @var boolean
	 */
	protected $trainingRecordings;
	
	/**
	 *
	 * @var boolean
	 */
	protected $recordedEvents;
	
	/**
	 *
	 * @var long
	 */
	protected $totalStorageSize;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myReports;
	
	/**
	 *
	 * @var long
	 */
	protected $myComputer;
	
	/**
	 *
	 * @var boolean
	 */
	protected $personalMeetingRoom;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myPartnerLinks;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myWorkspaces;
	
	/**
	 *
	 * @var long
	 */
	protected $additionalRecordingStorage;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'isMyWebExPro':
				return 'boolean';
	
			case 'myContact':
				return 'boolean';
	
			case 'myProfile':
				return 'boolean';
	
			case 'myMeetings':
				return 'boolean';
	
			case 'myFolders':
				return 'boolean';
	
			case 'trainingRecordings':
				return 'boolean';
	
			case 'recordedEvents':
				return 'boolean';
	
			case 'totalStorageSize':
				return 'long';
	
			case 'myReports':
				return 'boolean';
	
			case 'myComputer':
				return 'long';
	
			case 'personalMeetingRoom':
				return 'boolean';
	
			case 'myPartnerLinks':
				return 'boolean';
	
			case 'myWorkspaces':
				return 'boolean';
	
			case 'additionalRecordingStorage':
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
			'isMyWebExPro',
			'myContact',
			'myProfile',
			'myMeetings',
			'myFolders',
			'trainingRecordings',
			'recordedEvents',
			'totalStorageSize',
			'myReports',
			'myComputer',
			'personalMeetingRoom',
			'myPartnerLinks',
			'myWorkspaces',
			'additionalRecordingStorage',
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
		return 'mywebexType';
	}
	
	/**
	 * @param boolean $isMyWebExPro
	 */
	public function setIsMyWebExPro($isMyWebExPro)
	{
		$this->isMyWebExPro = $isMyWebExPro;
	}
	
	/**
	 * @return boolean $isMyWebExPro
	 */
	public function getIsMyWebExPro()
	{
		return $this->isMyWebExPro;
	}
	
	/**
	 * @param boolean $myContact
	 */
	public function setMyContact($myContact)
	{
		$this->myContact = $myContact;
	}
	
	/**
	 * @return boolean $myContact
	 */
	public function getMyContact()
	{
		return $this->myContact;
	}
	
	/**
	 * @param boolean $myProfile
	 */
	public function setMyProfile($myProfile)
	{
		$this->myProfile = $myProfile;
	}
	
	/**
	 * @return boolean $myProfile
	 */
	public function getMyProfile()
	{
		return $this->myProfile;
	}
	
	/**
	 * @param boolean $myMeetings
	 */
	public function setMyMeetings($myMeetings)
	{
		$this->myMeetings = $myMeetings;
	}
	
	/**
	 * @return boolean $myMeetings
	 */
	public function getMyMeetings()
	{
		return $this->myMeetings;
	}
	
	/**
	 * @param boolean $myFolders
	 */
	public function setMyFolders($myFolders)
	{
		$this->myFolders = $myFolders;
	}
	
	/**
	 * @return boolean $myFolders
	 */
	public function getMyFolders()
	{
		return $this->myFolders;
	}
	
	/**
	 * @param boolean $trainingRecordings
	 */
	public function setTrainingRecordings($trainingRecordings)
	{
		$this->trainingRecordings = $trainingRecordings;
	}
	
	/**
	 * @return boolean $trainingRecordings
	 */
	public function getTrainingRecordings()
	{
		return $this->trainingRecordings;
	}
	
	/**
	 * @param boolean $recordedEvents
	 */
	public function setRecordedEvents($recordedEvents)
	{
		$this->recordedEvents = $recordedEvents;
	}
	
	/**
	 * @return boolean $recordedEvents
	 */
	public function getRecordedEvents()
	{
		return $this->recordedEvents;
	}
	
	/**
	 * @param long $totalStorageSize
	 */
	public function setTotalStorageSize($totalStorageSize)
	{
		$this->totalStorageSize = $totalStorageSize;
	}
	
	/**
	 * @return long $totalStorageSize
	 */
	public function getTotalStorageSize()
	{
		return $this->totalStorageSize;
	}
	
	/**
	 * @param boolean $myReports
	 */
	public function setMyReports($myReports)
	{
		$this->myReports = $myReports;
	}
	
	/**
	 * @return boolean $myReports
	 */
	public function getMyReports()
	{
		return $this->myReports;
	}
	
	/**
	 * @param long $myComputer
	 */
	public function setMyComputer($myComputer)
	{
		$this->myComputer = $myComputer;
	}
	
	/**
	 * @return long $myComputer
	 */
	public function getMyComputer()
	{
		return $this->myComputer;
	}
	
	/**
	 * @param boolean $personalMeetingRoom
	 */
	public function setPersonalMeetingRoom($personalMeetingRoom)
	{
		$this->personalMeetingRoom = $personalMeetingRoom;
	}
	
	/**
	 * @return boolean $personalMeetingRoom
	 */
	public function getPersonalMeetingRoom()
	{
		return $this->personalMeetingRoom;
	}
	
	/**
	 * @param boolean $myPartnerLinks
	 */
	public function setMyPartnerLinks($myPartnerLinks)
	{
		$this->myPartnerLinks = $myPartnerLinks;
	}
	
	/**
	 * @return boolean $myPartnerLinks
	 */
	public function getMyPartnerLinks()
	{
		return $this->myPartnerLinks;
	}
	
	/**
	 * @param boolean $myWorkspaces
	 */
	public function setMyWorkspaces($myWorkspaces)
	{
		$this->myWorkspaces = $myWorkspaces;
	}
	
	/**
	 * @return boolean $myWorkspaces
	 */
	public function getMyWorkspaces()
	{
		return $this->myWorkspaces;
	}
	
	/**
	 * @param long $additionalRecordingStorage
	 */
	public function setAdditionalRecordingStorage($additionalRecordingStorage)
	{
		$this->additionalRecordingStorage = $additionalRecordingStorage;
	}
	
	/**
	 * @return long $additionalRecordingStorage
	 */
	public function getAdditionalRecordingStorage()
	{
		return $this->additionalRecordingStorage;
	}
	
}
		
