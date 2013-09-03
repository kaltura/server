<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlUserType.class.php');
require_once(__DIR__ . '/WebexXmlComAddressType.class.php');
require_once(__DIR__ . '/WebexXmlUseUserPhonesType.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXmlUseCommOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/long.class.php');
require_once(__DIR__ . '/WebexXmlUseOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlComTimeZoneType.class.php');
require_once(__DIR__ . '/WebexXmlComTrackingType.class.php');
require_once(__DIR__ . '/WebexXmlUseServiceType.class.php');
require_once(__DIR__ . '/WebexXmlUsePrivilegeType.class.php');
require_once(__DIR__ . '/WebexXmlUseActiveType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlUseTspAccountType.class.php');
require_once(__DIR__ . '/WebexXmlUseSupportedServicesType.class.php');
require_once(__DIR__ . '/WebexXmlUseMywebexType.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlUseThirdPartyAccountType.class.php');
require_once(__DIR__ . '/WebexXmlUsePersonalMeetingRoomType.class.php');
require_once(__DIR__ . '/WebexXmlUseSessionOptionsType.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXmlUseSecurityType.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXmlUseSharingAndRecordingType.class.php');

class WebexXmlUserTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $firstName;
	
	/**
	 *
	 * @var string
	 */
	protected $lastName;
	
	/**
	 *
	 * @var string
	 */
	protected $title;
	
	/**
	 *
	 * @var integer
	 */
	protected $categoryId;
	
	/**
	 *
	 * @var string
	 */
	protected $description;
	
	/**
	 *
	 * @var string
	 */
	protected $officeGreeting;
	
	/**
	 *
	 * @var string
	 */
	protected $company;
	
	/**
	 *
	 * @var string
	 */
	protected $webExId;
	
	/**
	 *
	 * @var WebexXmlComAddressType
	 */
	protected $address;
	
	/**
	 *
	 * @var WebexXmlUseUserPhonesType
	 */
	protected $phones;
	
	/**
	 *
	 * @var string
	 */
	protected $email;
	
	/**
	 *
	 * @var string
	 */
	protected $email2;
	
	/**
	 *
	 * @var string
	 */
	protected $officeurl;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $password;
	
	/**
	 *
	 * @var string
	 */
	protected $passwordHint;
	
	/**
	 *
	 * @var string
	 */
	protected $passwordHintAnswer;
	
	/**
	 *
	 * @var string
	 */
	protected $personalUrl;
	
	/**
	 *
	 * @var string
	 */
	protected $expirationDate;
	
	/**
	 *
	 * @var WebexXmlUseCommOptionsType
	 */
	protected $commOptions;
	
	/**
	 *
	 * @var WebexXmlArray<long>
	 */
	protected $meetingTypes;
	
	/**
	 *
	 * @var WebexXmlUseOptionsType
	 */
	protected $options;
	
	/**
	 *
	 * @var long
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var WebexXmlComTimeZoneType
	 */
	protected $timeZone;
	
	/**
	 *
	 * @var string
	 */
	protected $timeZoneWithDST;
	
	/**
	 *
	 * @var WebexXmlComTrackingType
	 */
	protected $tracking;
	
	/**
	 *
	 * @var WebexXmlUseServiceType
	 */
	protected $service;
	
	/**
	 *
	 * @var WebexXmlUsePrivilegeType
	 */
	protected $privilege;
	
	/**
	 *
	 * @var string
	 */
	protected $language;
	
	/**
	 *
	 * @var string
	 */
	protected $locale;
	
	/**
	 *
	 * @var string
	 */
	protected $schedulingPermission;
	
	/**
	 *
	 * @var WebexXmlUseActiveType
	 */
	protected $active;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlUseTspAccountType>
	 */
	protected $tspAccount;
	
	/**
	 *
	 * @var WebexXmlUseSupportedServicesType
	 */
	protected $supportedServices;
	
	/**
	 *
	 * @var WebexXmlUseMywebexType
	 */
	protected $myWebEx;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $personalTeleconf;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlUseThirdPartyAccountType>
	 */
	protected $thirdPartyTeleconf;
	
	/**
	 *
	 * @var WebexXmlUsePersonalMeetingRoomType
	 */
	protected $personalMeetingRoom;
	
	/**
	 *
	 * @var WebexXmlUseSessionOptionsType
	 */
	protected $sessionOptions;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $supportCenter;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $mpProfileNumber;
	
	/**
	 *
	 * @var WebexXmlUseSecurityType
	 */
	protected $security;
	
	/**
	 *
	 * @var long
	 */
	protected $languageID;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $webACDPrefs;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $remoteSupport;
	
	/**
	 *
	 * @var WebexXmlUseSharingAndRecordingType
	 */
	protected $remoteAccess;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'firstName',
			'lastName',
			'title',
			'categoryId',
			'description',
			'officeGreeting',
			'company',
			'webExId',
			'address',
			'phones',
			'email',
			'email2',
			'officeurl',
			'password',
			'passwordHint',
			'passwordHintAnswer',
			'personalUrl',
			'expirationDate',
			'commOptions',
			'meetingTypes',
			'options',
			'timeZoneID',
			'timeZone',
			'timeZoneWithDST',
			'tracking',
			'service',
			'privilege',
			'language',
			'locale',
			'schedulingPermission',
			'active',
			'tspAccount',
			'supportedServices',
			'myWebEx',
			'personalTeleconf',
			'thirdPartyTeleconf',
			'personalMeetingRoom',
			'sessionOptions',
			'supportCenter',
			'mpProfileNumber',
			'security',
			'languageID',
			'webACDPrefs',
			'remoteSupport',
			'remoteAccess',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'webExId',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'use';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'use:userType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlUserType';
	}
	
	/**
	 * @param string $firstName
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}
	
	/**
	 * @param string $lastName
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	/**
	 * @param integer $categoryId
	 */
	public function setCategoryId($categoryId)
	{
		$this->categoryId = $categoryId;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @param string $officeGreeting
	 */
	public function setOfficeGreeting($officeGreeting)
	{
		$this->officeGreeting = $officeGreeting;
	}
	
	/**
	 * @param string $company
	 */
	public function setCompany($company)
	{
		$this->company = $company;
	}
	
	/**
	 * @param string $webExId
	 */
	public function setWebExId($webExId)
	{
		$this->webExId = $webExId;
	}
	
	/**
	 * @param WebexXmlComAddressType $address
	 */
	public function setAddress(WebexXmlComAddressType $address)
	{
		$this->address = $address;
	}
	
	/**
	 * @param WebexXmlUseUserPhonesType $phones
	 */
	public function setPhones(WebexXmlUseUserPhonesType $phones)
	{
		$this->phones = $phones;
	}
	
	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}
	
	/**
	 * @param string $email2
	 */
	public function setEmail2($email2)
	{
		$this->email2 = $email2;
	}
	
	/**
	 * @param string $officeurl
	 */
	public function setOfficeurl($officeurl)
	{
		$this->officeurl = $officeurl;
	}
	
	/**
	 * @param WebexXml $password
	 */
	public function setPassword(WebexXml $password)
	{
		$this->password = $password;
	}
	
	/**
	 * @param string $passwordHint
	 */
	public function setPasswordHint($passwordHint)
	{
		$this->passwordHint = $passwordHint;
	}
	
	/**
	 * @param string $passwordHintAnswer
	 */
	public function setPasswordHintAnswer($passwordHintAnswer)
	{
		$this->passwordHintAnswer = $passwordHintAnswer;
	}
	
	/**
	 * @param string $personalUrl
	 */
	public function setPersonalUrl($personalUrl)
	{
		$this->personalUrl = $personalUrl;
	}
	
	/**
	 * @param string $expirationDate
	 */
	public function setExpirationDate($expirationDate)
	{
		$this->expirationDate = $expirationDate;
	}
	
	/**
	 * @param WebexXmlUseCommOptionsType $commOptions
	 */
	public function setCommOptions(WebexXmlUseCommOptionsType $commOptions)
	{
		$this->commOptions = $commOptions;
	}
	
	/**
	 * @param WebexXmlArray<long> $meetingTypes
	 */
	public function setMeetingTypes(WebexXmlArray $meetingTypes)
	{
		if($meetingTypes->getType() != 'long')
			throw new WebexXmlException(get_class($this) . "::meetingTypes must be of type long");
		
		$this->meetingTypes = $meetingTypes;
	}
	
	/**
	 * @param WebexXmlUseOptionsType $options
	 */
	public function setOptions(WebexXmlUseOptionsType $options)
	{
		$this->options = $options;
	}
	
	/**
	 * @param long $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @param WebexXmlComTimeZoneType $timeZone
	 */
	public function setTimeZone(WebexXmlComTimeZoneType $timeZone)
	{
		$this->timeZone = $timeZone;
	}
	
	/**
	 * @param string $timeZoneWithDST
	 */
	public function setTimeZoneWithDST($timeZoneWithDST)
	{
		$this->timeZoneWithDST = $timeZoneWithDST;
	}
	
	/**
	 * @param WebexXmlComTrackingType $tracking
	 */
	public function setTracking(WebexXmlComTrackingType $tracking)
	{
		$this->tracking = $tracking;
	}
	
	/**
	 * @param WebexXmlUseServiceType $service
	 */
	public function setService(WebexXmlUseServiceType $service)
	{
		$this->service = $service;
	}
	
	/**
	 * @param WebexXmlUsePrivilegeType $privilege
	 */
	public function setPrivilege(WebexXmlUsePrivilegeType $privilege)
	{
		$this->privilege = $privilege;
	}
	
	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}
	
	/**
	 * @param string $locale
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;
	}
	
	/**
	 * @param string $schedulingPermission
	 */
	public function setSchedulingPermission($schedulingPermission)
	{
		$this->schedulingPermission = $schedulingPermission;
	}
	
	/**
	 * @param WebexXmlUseActiveType $active
	 */
	public function setActive(WebexXmlUseActiveType $active)
	{
		$this->active = $active;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlUseTspAccountType> $tspAccount
	 */
	public function setTspAccount(WebexXmlArray $tspAccount)
	{
		if($tspAccount->getType() != 'WebexXmlUseTspAccountType')
			throw new WebexXmlException(get_class($this) . "::tspAccount must be of type WebexXmlUseTspAccountType");
		
		$this->tspAccount = $tspAccount;
	}
	
	/**
	 * @param WebexXmlUseSupportedServicesType $supportedServices
	 */
	public function setSupportedServices(WebexXmlUseSupportedServicesType $supportedServices)
	{
		$this->supportedServices = $supportedServices;
	}
	
	/**
	 * @param WebexXmlUseMywebexType $myWebEx
	 */
	public function setMyWebEx(WebexXmlUseMywebexType $myWebEx)
	{
		$this->myWebEx = $myWebEx;
	}
	
	/**
	 * @param WebexXml $personalTeleconf
	 */
	public function setPersonalTeleconf(WebexXml $personalTeleconf)
	{
		$this->personalTeleconf = $personalTeleconf;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlUseThirdPartyAccountType> $thirdPartyTeleconf
	 */
	public function setThirdPartyTeleconf(WebexXmlArray $thirdPartyTeleconf)
	{
		if($thirdPartyTeleconf->getType() != 'WebexXmlUseThirdPartyAccountType')
			throw new WebexXmlException(get_class($this) . "::thirdPartyTeleconf must be of type WebexXmlUseThirdPartyAccountType");
		
		$this->thirdPartyTeleconf = $thirdPartyTeleconf;
	}
	
	/**
	 * @param WebexXmlUsePersonalMeetingRoomType $personalMeetingRoom
	 */
	public function setPersonalMeetingRoom(WebexXmlUsePersonalMeetingRoomType $personalMeetingRoom)
	{
		$this->personalMeetingRoom = $personalMeetingRoom;
	}
	
	/**
	 * @param WebexXmlUseSessionOptionsType $sessionOptions
	 */
	public function setSessionOptions(WebexXmlUseSessionOptionsType $sessionOptions)
	{
		$this->sessionOptions = $sessionOptions;
	}
	
	/**
	 * @param WebexXml $supportCenter
	 */
	public function setSupportCenter(WebexXml $supportCenter)
	{
		$this->supportCenter = $supportCenter;
	}
	
	/**
	 * @param WebexXml $mpProfileNumber
	 */
	public function setMpProfileNumber(WebexXml $mpProfileNumber)
	{
		$this->mpProfileNumber = $mpProfileNumber;
	}
	
	/**
	 * @param WebexXmlUseSecurityType $security
	 */
	public function setSecurity(WebexXmlUseSecurityType $security)
	{
		$this->security = $security;
	}
	
	/**
	 * @param long $languageID
	 */
	public function setLanguageID($languageID)
	{
		$this->languageID = $languageID;
	}
	
	/**
	 * @param WebexXml $webACDPrefs
	 */
	public function setWebACDPrefs(WebexXml $webACDPrefs)
	{
		$this->webACDPrefs = $webACDPrefs;
	}
	
	/**
	 * @param WebexXml $remoteSupport
	 */
	public function setRemoteSupport(WebexXml $remoteSupport)
	{
		$this->remoteSupport = $remoteSupport;
	}
	
	/**
	 * @param WebexXmlUseSharingAndRecordingType $remoteAccess
	 */
	public function setRemoteAccess(WebexXmlUseSharingAndRecordingType $remoteAccess)
	{
		$this->remoteAccess = $remoteAccess;
	}
	
}

