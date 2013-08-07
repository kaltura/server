<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteMyWebExConfigType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $myContacts;
	
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
	protected $trainingRecordings;
	
	/**
	 *
	 * @var boolean
	 */
	protected $folders;
	
	/**
	 *
	 * @var boolean
	 */
	protected $eventDocument;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myReport;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myComputer;
	
	/**
	 *
	 * @var boolean
	 */
	protected $personalMeetingPage;
	
	/**
	 *
	 * @var long
	 */
	protected $myFilesStorage;
	
	/**
	 *
	 * @var long
	 */
	protected $myComputerNumbers;
	
	/**
	 *
	 * @var boolean
	 */
	protected $enableMyWebExPro;
	
	/**
	 *
	 * @var WebexXmlSiteLimitsType
	 */
	protected $myWebExProHostLimit;
	
	/**
	 *
	 * @var long
	 */
	protected $myWebExProMaxHosts;
	
	/**
	 *
	 * @var boolean
	 */
	protected $restrictAccessAnyApps;
	
	/**
	 *
	 * @var long
	 */
	protected $restrictAccessAnyAppsNum;
	
	/**
	 *
	 * @var WebexXmlSiteLimitsType
	 */
	protected $addlAccessAnyComputersLimit;
	
	/**
	 *
	 * @var long
	 */
	protected $addlAccessAnyComputers;
	
	/**
	 *
	 * @var WebexXmlSiteLimitsType
	 */
	protected $addlStorageLimit;
	
	/**
	 *
	 * @var long
	 */
	protected $addlStorage;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myContactsPro;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myProfilePro;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myMeetingsPro;
	
	/**
	 *
	 * @var boolean
	 */
	protected $trainingRecordingsPro;
	
	/**
	 *
	 * @var boolean
	 */
	protected $foldersPro;
	
	/**
	 *
	 * @var boolean
	 */
	protected $eventDocumentPro;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myReportPro;
	
	/**
	 *
	 * @var boolean
	 */
	protected $myComputerPro;
	
	/**
	 *
	 * @var boolean
	 */
	protected $personalMeetingPagePro;
	
	/**
	 *
	 * @var long
	 */
	protected $myFilesStoragePro;
	
	/**
	 *
	 * @var long
	 */
	protected $myComputerNumbersPro;
	
	/**
	 *
	 * @var boolean
	 */
	protected $PMRheaderBranding;
	
	/**
	 *
	 * @var WebexXmlSiteHeaderImageLocationType
	 */
	protected $PMRheaderBrandingLocation;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'myContacts':
				return 'boolean';
	
			case 'myProfile':
				return 'boolean';
	
			case 'myMeetings':
				return 'boolean';
	
			case 'trainingRecordings':
				return 'boolean';
	
			case 'folders':
				return 'boolean';
	
			case 'eventDocument':
				return 'boolean';
	
			case 'myReport':
				return 'boolean';
	
			case 'myComputer':
				return 'boolean';
	
			case 'personalMeetingPage':
				return 'boolean';
	
			case 'myFilesStorage':
				return 'long';
	
			case 'myComputerNumbers':
				return 'long';
	
			case 'enableMyWebExPro':
				return 'boolean';
	
			case 'myWebExProHostLimit':
				return 'WebexXmlSiteLimitsType';
	
			case 'myWebExProMaxHosts':
				return 'long';
	
			case 'restrictAccessAnyApps':
				return 'boolean';
	
			case 'restrictAccessAnyAppsNum':
				return 'long';
	
			case 'addlAccessAnyComputersLimit':
				return 'WebexXmlSiteLimitsType';
	
			case 'addlAccessAnyComputers':
				return 'long';
	
			case 'addlStorageLimit':
				return 'WebexXmlSiteLimitsType';
	
			case 'addlStorage':
				return 'long';
	
			case 'myContactsPro':
				return 'boolean';
	
			case 'myProfilePro':
				return 'boolean';
	
			case 'myMeetingsPro':
				return 'boolean';
	
			case 'trainingRecordingsPro':
				return 'boolean';
	
			case 'foldersPro':
				return 'boolean';
	
			case 'eventDocumentPro':
				return 'boolean';
	
			case 'myReportPro':
				return 'boolean';
	
			case 'myComputerPro':
				return 'boolean';
	
			case 'personalMeetingPagePro':
				return 'boolean';
	
			case 'myFilesStoragePro':
				return 'long';
	
			case 'myComputerNumbersPro':
				return 'long';
	
			case 'PMRheaderBranding':
				return 'boolean';
	
			case 'PMRheaderBrandingLocation':
				return 'WebexXmlSiteHeaderImageLocationType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'myContacts',
			'myProfile',
			'myMeetings',
			'trainingRecordings',
			'folders',
			'eventDocument',
			'myReport',
			'myComputer',
			'personalMeetingPage',
			'myFilesStorage',
			'myComputerNumbers',
			'enableMyWebExPro',
			'myWebExProHostLimit',
			'myWebExProMaxHosts',
			'restrictAccessAnyApps',
			'restrictAccessAnyAppsNum',
			'addlAccessAnyComputersLimit',
			'addlAccessAnyComputers',
			'addlStorageLimit',
			'addlStorage',
			'myContactsPro',
			'myProfilePro',
			'myMeetingsPro',
			'trainingRecordingsPro',
			'foldersPro',
			'eventDocumentPro',
			'myReportPro',
			'myComputerPro',
			'personalMeetingPagePro',
			'myFilesStoragePro',
			'myComputerNumbersPro',
			'PMRheaderBranding',
			'PMRheaderBrandingLocation',
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
		return 'myWebExConfigType';
	}
	
	/**
	 * @param boolean $myContacts
	 */
	public function setMyContacts($myContacts)
	{
		$this->myContacts = $myContacts;
	}
	
	/**
	 * @return boolean $myContacts
	 */
	public function getMyContacts()
	{
		return $this->myContacts;
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
	 * @param boolean $folders
	 */
	public function setFolders($folders)
	{
		$this->folders = $folders;
	}
	
	/**
	 * @return boolean $folders
	 */
	public function getFolders()
	{
		return $this->folders;
	}
	
	/**
	 * @param boolean $eventDocument
	 */
	public function setEventDocument($eventDocument)
	{
		$this->eventDocument = $eventDocument;
	}
	
	/**
	 * @return boolean $eventDocument
	 */
	public function getEventDocument()
	{
		return $this->eventDocument;
	}
	
	/**
	 * @param boolean $myReport
	 */
	public function setMyReport($myReport)
	{
		$this->myReport = $myReport;
	}
	
	/**
	 * @return boolean $myReport
	 */
	public function getMyReport()
	{
		return $this->myReport;
	}
	
	/**
	 * @param boolean $myComputer
	 */
	public function setMyComputer($myComputer)
	{
		$this->myComputer = $myComputer;
	}
	
	/**
	 * @return boolean $myComputer
	 */
	public function getMyComputer()
	{
		return $this->myComputer;
	}
	
	/**
	 * @param boolean $personalMeetingPage
	 */
	public function setPersonalMeetingPage($personalMeetingPage)
	{
		$this->personalMeetingPage = $personalMeetingPage;
	}
	
	/**
	 * @return boolean $personalMeetingPage
	 */
	public function getPersonalMeetingPage()
	{
		return $this->personalMeetingPage;
	}
	
	/**
	 * @param long $myFilesStorage
	 */
	public function setMyFilesStorage($myFilesStorage)
	{
		$this->myFilesStorage = $myFilesStorage;
	}
	
	/**
	 * @return long $myFilesStorage
	 */
	public function getMyFilesStorage()
	{
		return $this->myFilesStorage;
	}
	
	/**
	 * @param long $myComputerNumbers
	 */
	public function setMyComputerNumbers($myComputerNumbers)
	{
		$this->myComputerNumbers = $myComputerNumbers;
	}
	
	/**
	 * @return long $myComputerNumbers
	 */
	public function getMyComputerNumbers()
	{
		return $this->myComputerNumbers;
	}
	
	/**
	 * @param boolean $enableMyWebExPro
	 */
	public function setEnableMyWebExPro($enableMyWebExPro)
	{
		$this->enableMyWebExPro = $enableMyWebExPro;
	}
	
	/**
	 * @return boolean $enableMyWebExPro
	 */
	public function getEnableMyWebExPro()
	{
		return $this->enableMyWebExPro;
	}
	
	/**
	 * @param WebexXmlSiteLimitsType $myWebExProHostLimit
	 */
	public function setMyWebExProHostLimit(WebexXmlSiteLimitsType $myWebExProHostLimit)
	{
		$this->myWebExProHostLimit = $myWebExProHostLimit;
	}
	
	/**
	 * @return WebexXmlSiteLimitsType $myWebExProHostLimit
	 */
	public function getMyWebExProHostLimit()
	{
		return $this->myWebExProHostLimit;
	}
	
	/**
	 * @param long $myWebExProMaxHosts
	 */
	public function setMyWebExProMaxHosts($myWebExProMaxHosts)
	{
		$this->myWebExProMaxHosts = $myWebExProMaxHosts;
	}
	
	/**
	 * @return long $myWebExProMaxHosts
	 */
	public function getMyWebExProMaxHosts()
	{
		return $this->myWebExProMaxHosts;
	}
	
	/**
	 * @param boolean $restrictAccessAnyApps
	 */
	public function setRestrictAccessAnyApps($restrictAccessAnyApps)
	{
		$this->restrictAccessAnyApps = $restrictAccessAnyApps;
	}
	
	/**
	 * @return boolean $restrictAccessAnyApps
	 */
	public function getRestrictAccessAnyApps()
	{
		return $this->restrictAccessAnyApps;
	}
	
	/**
	 * @param long $restrictAccessAnyAppsNum
	 */
	public function setRestrictAccessAnyAppsNum($restrictAccessAnyAppsNum)
	{
		$this->restrictAccessAnyAppsNum = $restrictAccessAnyAppsNum;
	}
	
	/**
	 * @return long $restrictAccessAnyAppsNum
	 */
	public function getRestrictAccessAnyAppsNum()
	{
		return $this->restrictAccessAnyAppsNum;
	}
	
	/**
	 * @param WebexXmlSiteLimitsType $addlAccessAnyComputersLimit
	 */
	public function setAddlAccessAnyComputersLimit(WebexXmlSiteLimitsType $addlAccessAnyComputersLimit)
	{
		$this->addlAccessAnyComputersLimit = $addlAccessAnyComputersLimit;
	}
	
	/**
	 * @return WebexXmlSiteLimitsType $addlAccessAnyComputersLimit
	 */
	public function getAddlAccessAnyComputersLimit()
	{
		return $this->addlAccessAnyComputersLimit;
	}
	
	/**
	 * @param long $addlAccessAnyComputers
	 */
	public function setAddlAccessAnyComputers($addlAccessAnyComputers)
	{
		$this->addlAccessAnyComputers = $addlAccessAnyComputers;
	}
	
	/**
	 * @return long $addlAccessAnyComputers
	 */
	public function getAddlAccessAnyComputers()
	{
		return $this->addlAccessAnyComputers;
	}
	
	/**
	 * @param WebexXmlSiteLimitsType $addlStorageLimit
	 */
	public function setAddlStorageLimit(WebexXmlSiteLimitsType $addlStorageLimit)
	{
		$this->addlStorageLimit = $addlStorageLimit;
	}
	
	/**
	 * @return WebexXmlSiteLimitsType $addlStorageLimit
	 */
	public function getAddlStorageLimit()
	{
		return $this->addlStorageLimit;
	}
	
	/**
	 * @param long $addlStorage
	 */
	public function setAddlStorage($addlStorage)
	{
		$this->addlStorage = $addlStorage;
	}
	
	/**
	 * @return long $addlStorage
	 */
	public function getAddlStorage()
	{
		return $this->addlStorage;
	}
	
	/**
	 * @param boolean $myContactsPro
	 */
	public function setMyContactsPro($myContactsPro)
	{
		$this->myContactsPro = $myContactsPro;
	}
	
	/**
	 * @return boolean $myContactsPro
	 */
	public function getMyContactsPro()
	{
		return $this->myContactsPro;
	}
	
	/**
	 * @param boolean $myProfilePro
	 */
	public function setMyProfilePro($myProfilePro)
	{
		$this->myProfilePro = $myProfilePro;
	}
	
	/**
	 * @return boolean $myProfilePro
	 */
	public function getMyProfilePro()
	{
		return $this->myProfilePro;
	}
	
	/**
	 * @param boolean $myMeetingsPro
	 */
	public function setMyMeetingsPro($myMeetingsPro)
	{
		$this->myMeetingsPro = $myMeetingsPro;
	}
	
	/**
	 * @return boolean $myMeetingsPro
	 */
	public function getMyMeetingsPro()
	{
		return $this->myMeetingsPro;
	}
	
	/**
	 * @param boolean $trainingRecordingsPro
	 */
	public function setTrainingRecordingsPro($trainingRecordingsPro)
	{
		$this->trainingRecordingsPro = $trainingRecordingsPro;
	}
	
	/**
	 * @return boolean $trainingRecordingsPro
	 */
	public function getTrainingRecordingsPro()
	{
		return $this->trainingRecordingsPro;
	}
	
	/**
	 * @param boolean $foldersPro
	 */
	public function setFoldersPro($foldersPro)
	{
		$this->foldersPro = $foldersPro;
	}
	
	/**
	 * @return boolean $foldersPro
	 */
	public function getFoldersPro()
	{
		return $this->foldersPro;
	}
	
	/**
	 * @param boolean $eventDocumentPro
	 */
	public function setEventDocumentPro($eventDocumentPro)
	{
		$this->eventDocumentPro = $eventDocumentPro;
	}
	
	/**
	 * @return boolean $eventDocumentPro
	 */
	public function getEventDocumentPro()
	{
		return $this->eventDocumentPro;
	}
	
	/**
	 * @param boolean $myReportPro
	 */
	public function setMyReportPro($myReportPro)
	{
		$this->myReportPro = $myReportPro;
	}
	
	/**
	 * @return boolean $myReportPro
	 */
	public function getMyReportPro()
	{
		return $this->myReportPro;
	}
	
	/**
	 * @param boolean $myComputerPro
	 */
	public function setMyComputerPro($myComputerPro)
	{
		$this->myComputerPro = $myComputerPro;
	}
	
	/**
	 * @return boolean $myComputerPro
	 */
	public function getMyComputerPro()
	{
		return $this->myComputerPro;
	}
	
	/**
	 * @param boolean $personalMeetingPagePro
	 */
	public function setPersonalMeetingPagePro($personalMeetingPagePro)
	{
		$this->personalMeetingPagePro = $personalMeetingPagePro;
	}
	
	/**
	 * @return boolean $personalMeetingPagePro
	 */
	public function getPersonalMeetingPagePro()
	{
		return $this->personalMeetingPagePro;
	}
	
	/**
	 * @param long $myFilesStoragePro
	 */
	public function setMyFilesStoragePro($myFilesStoragePro)
	{
		$this->myFilesStoragePro = $myFilesStoragePro;
	}
	
	/**
	 * @return long $myFilesStoragePro
	 */
	public function getMyFilesStoragePro()
	{
		return $this->myFilesStoragePro;
	}
	
	/**
	 * @param long $myComputerNumbersPro
	 */
	public function setMyComputerNumbersPro($myComputerNumbersPro)
	{
		$this->myComputerNumbersPro = $myComputerNumbersPro;
	}
	
	/**
	 * @return long $myComputerNumbersPro
	 */
	public function getMyComputerNumbersPro()
	{
		return $this->myComputerNumbersPro;
	}
	
	/**
	 * @param boolean $PMRheaderBranding
	 */
	public function setPMRheaderBranding($PMRheaderBranding)
	{
		$this->PMRheaderBranding = $PMRheaderBranding;
	}
	
	/**
	 * @return boolean $PMRheaderBranding
	 */
	public function getPMRheaderBranding()
	{
		return $this->PMRheaderBranding;
	}
	
	/**
	 * @param WebexXmlSiteHeaderImageLocationType $PMRheaderBrandingLocation
	 */
	public function setPMRheaderBrandingLocation(WebexXmlSiteHeaderImageLocationType $PMRheaderBrandingLocation)
	{
		$this->PMRheaderBrandingLocation = $PMRheaderBrandingLocation;
	}
	
	/**
	 * @return WebexXmlSiteHeaderImageLocationType $PMRheaderBrandingLocation
	 */
	public function getPMRheaderBrandingLocation()
	{
		return $this->PMRheaderBrandingLocation;
	}
	
}
		
