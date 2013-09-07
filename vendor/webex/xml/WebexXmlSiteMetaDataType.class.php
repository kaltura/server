<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteMetaDataType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $isEnterprise;
	
	/**
	 *
	 * @var WebexXmlArray<string>
	 */
	protected $serviceType;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXml>
	 */
	protected $meetingTypes;
	
	/**
	 *
	 * @var string
	 */
	protected $siteName;
	
	/**
	 *
	 * @var WebexXmlArray<string>
	 */
	protected $brandName;
	
	/**
	 *
	 * @var string
	 */
	protected $region;
	
	/**
	 *
	 * @var string
	 */
	protected $currency;
	
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
	protected $parterID;
	
	/**
	 *
	 * @var string
	 */
	protected $webDomain;
	
	/**
	 *
	 * @var string
	 */
	protected $meetingDomain;
	
	/**
	 *
	 * @var string
	 */
	protected $telephonyDomain;
	
	/**
	 *
	 * @var string
	 */
	protected $pageVersion;
	
	/**
	 *
	 * @var string
	 */
	protected $clientVersion;
	
	/**
	 *
	 * @var string
	 */
	protected $pageLanguage;
	
	/**
	 *
	 * @var boolean
	 */
	protected $activateStatus;
	
	/**
	 *
	 * @var WebexXmlSiteWebPageTypeType
	 */
	protected $webPageType;
	
	/**
	 *
	 * @var boolean
	 */
	protected $iCalendar;
	
	/**
	 *
	 * @var WebexXmlSiteMyWebExPageType
	 */
	protected $myWebExDefaultPage;
	
	/**
	 *
	 * @var string
	 */
	protected $componentVersion;
	
	/**
	 *
	 * @var long
	 */
	protected $accountNumLimit;
	
	/**
	 *
	 * @var long
	 */
	protected $activeUserCount;
	
	/**
	 *
	 * @var long
	 */
	protected $auoAccountNumLimit;
	
	/**
	 *
	 * @var long
	 */
	protected $auoActiveUserCount;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayMeetingActualTime;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayOffset;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportWebEx11;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'isEnterprise':
				return 'boolean';
	
			case 'serviceType':
				return 'WebexXmlArray<string>';
	
			case 'meetingTypes':
				return 'WebexXmlArray<WebexXml>';
	
			case 'siteName':
				return 'string';
	
			case 'brandName':
				return 'WebexXmlArray<string>';
	
			case 'region':
				return 'string';
	
			case 'currency':
				return 'string';
	
			case 'timeZoneID':
				return 'long';
	
			case 'timeZone':
				return 'WebexXmlComTimeZoneType';
	
			case 'parterID':
				return 'string';
	
			case 'webDomain':
				return 'string';
	
			case 'meetingDomain':
				return 'string';
	
			case 'telephonyDomain':
				return 'string';
	
			case 'pageVersion':
				return 'string';
	
			case 'clientVersion':
				return 'string';
	
			case 'pageLanguage':
				return 'string';
	
			case 'activateStatus':
				return 'boolean';
	
			case 'webPageType':
				return 'WebexXmlSiteWebPageTypeType';
	
			case 'iCalendar':
				return 'boolean';
	
			case 'myWebExDefaultPage':
				return 'WebexXmlSiteMyWebExPageType';
	
			case 'componentVersion':
				return 'string';
	
			case 'accountNumLimit':
				return 'long';
	
			case 'activeUserCount':
				return 'long';
	
			case 'auoAccountNumLimit':
				return 'long';
	
			case 'auoActiveUserCount':
				return 'long';
	
			case 'displayMeetingActualTime':
				return 'boolean';
	
			case 'displayOffset':
				return 'boolean';
	
			case 'supportWebEx11':
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
			'isEnterprise',
			'serviceType',
			'meetingTypes',
			'siteName',
			'brandName',
			'region',
			'currency',
			'timeZoneID',
			'timeZone',
			'parterID',
			'webDomain',
			'meetingDomain',
			'telephonyDomain',
			'pageVersion',
			'clientVersion',
			'pageLanguage',
			'activateStatus',
			'webPageType',
			'iCalendar',
			'myWebExDefaultPage',
			'componentVersion',
			'accountNumLimit',
			'activeUserCount',
			'auoAccountNumLimit',
			'auoActiveUserCount',
			'displayMeetingActualTime',
			'displayOffset',
			'supportWebEx11',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'isEnterprise',
			'serviceType',
			'siteName',
			'region',
			'currency',
			'timeZoneID',
			'parterID',
			'webDomain',
			'meetingDomain',
			'telephonyDomain',
			'pageVersion',
			'clientVersion',
			'pageLanguage',
			'activateStatus',
			'supportWebEx11',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'metaDataType';
	}
	
	/**
	 * @param boolean $isEnterprise
	 */
	public function setIsEnterprise($isEnterprise)
	{
		$this->isEnterprise = $isEnterprise;
	}
	
	/**
	 * @return boolean $isEnterprise
	 */
	public function getIsEnterprise()
	{
		return $this->isEnterprise;
	}
	
	/**
	 * @param WebexXmlArray<string> $serviceType
	 */
	public function setServiceType($serviceType)
	{
		if($serviceType->getType() != 'string')
			throw new WebexXmlException(get_class($this) . "::serviceType must be of type string");
		
		$this->serviceType = $serviceType;
	}
	
	/**
	 * @return WebexXmlArray $serviceType
	 */
	public function getServiceType()
	{
		return $this->serviceType;
	}
	
	/**
	 * @param WebexXmlArray<WebexXml> $meetingTypes
	 */
	public function setMeetingTypes(WebexXmlArray $meetingTypes)
	{
		if($meetingTypes->getType() != 'WebexXml')
			throw new WebexXmlException(get_class($this) . "::meetingTypes must be of type WebexXml");
		
		$this->meetingTypes = $meetingTypes;
	}
	
	/**
	 * @return WebexXmlArray $meetingTypes
	 */
	public function getMeetingTypes()
	{
		return $this->meetingTypes;
	}
	
	/**
	 * @param string $siteName
	 */
	public function setSiteName($siteName)
	{
		$this->siteName = $siteName;
	}
	
	/**
	 * @return string $siteName
	 */
	public function getSiteName()
	{
		return $this->siteName;
	}
	
	/**
	 * @param WebexXmlArray<string> $brandName
	 */
	public function setBrandName($brandName)
	{
		if($brandName->getType() != 'string')
			throw new WebexXmlException(get_class($this) . "::brandName must be of type string");
		
		$this->brandName = $brandName;
	}
	
	/**
	 * @return WebexXmlArray $brandName
	 */
	public function getBrandName()
	{
		return $this->brandName;
	}
	
	/**
	 * @param string $region
	 */
	public function setRegion($region)
	{
		$this->region = $region;
	}
	
	/**
	 * @return string $region
	 */
	public function getRegion()
	{
		return $this->region;
	}
	
	/**
	 * @param string $currency
	 */
	public function setCurrency($currency)
	{
		$this->currency = $currency;
	}
	
	/**
	 * @return string $currency
	 */
	public function getCurrency()
	{
		return $this->currency;
	}
	
	/**
	 * @param long $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @return long $timeZoneID
	 */
	public function getTimeZoneID()
	{
		return $this->timeZoneID;
	}
	
	/**
	 * @param WebexXmlComTimeZoneType $timeZone
	 */
	public function setTimeZone(WebexXmlComTimeZoneType $timeZone)
	{
		$this->timeZone = $timeZone;
	}
	
	/**
	 * @return WebexXmlComTimeZoneType $timeZone
	 */
	public function getTimeZone()
	{
		return $this->timeZone;
	}
	
	/**
	 * @param string $parterID
	 */
	public function setParterID($parterID)
	{
		$this->parterID = $parterID;
	}
	
	/**
	 * @return string $parterID
	 */
	public function getParterID()
	{
		return $this->parterID;
	}
	
	/**
	 * @param string $webDomain
	 */
	public function setWebDomain($webDomain)
	{
		$this->webDomain = $webDomain;
	}
	
	/**
	 * @return string $webDomain
	 */
	public function getWebDomain()
	{
		return $this->webDomain;
	}
	
	/**
	 * @param string $meetingDomain
	 */
	public function setMeetingDomain($meetingDomain)
	{
		$this->meetingDomain = $meetingDomain;
	}
	
	/**
	 * @return string $meetingDomain
	 */
	public function getMeetingDomain()
	{
		return $this->meetingDomain;
	}
	
	/**
	 * @param string $telephonyDomain
	 */
	public function setTelephonyDomain($telephonyDomain)
	{
		$this->telephonyDomain = $telephonyDomain;
	}
	
	/**
	 * @return string $telephonyDomain
	 */
	public function getTelephonyDomain()
	{
		return $this->telephonyDomain;
	}
	
	/**
	 * @param string $pageVersion
	 */
	public function setPageVersion($pageVersion)
	{
		$this->pageVersion = $pageVersion;
	}
	
	/**
	 * @return string $pageVersion
	 */
	public function getPageVersion()
	{
		return $this->pageVersion;
	}
	
	/**
	 * @param string $clientVersion
	 */
	public function setClientVersion($clientVersion)
	{
		$this->clientVersion = $clientVersion;
	}
	
	/**
	 * @return string $clientVersion
	 */
	public function getClientVersion()
	{
		return $this->clientVersion;
	}
	
	/**
	 * @param string $pageLanguage
	 */
	public function setPageLanguage($pageLanguage)
	{
		$this->pageLanguage = $pageLanguage;
	}
	
	/**
	 * @return string $pageLanguage
	 */
	public function getPageLanguage()
	{
		return $this->pageLanguage;
	}
	
	/**
	 * @param boolean $activateStatus
	 */
	public function setActivateStatus($activateStatus)
	{
		$this->activateStatus = $activateStatus;
	}
	
	/**
	 * @return boolean $activateStatus
	 */
	public function getActivateStatus()
	{
		return $this->activateStatus;
	}
	
	/**
	 * @param WebexXmlSiteWebPageTypeType $webPageType
	 */
	public function setWebPageType(WebexXmlSiteWebPageTypeType $webPageType)
	{
		$this->webPageType = $webPageType;
	}
	
	/**
	 * @return WebexXmlSiteWebPageTypeType $webPageType
	 */
	public function getWebPageType()
	{
		return $this->webPageType;
	}
	
	/**
	 * @param boolean $iCalendar
	 */
	public function setICalendar($iCalendar)
	{
		$this->iCalendar = $iCalendar;
	}
	
	/**
	 * @return boolean $iCalendar
	 */
	public function getICalendar()
	{
		return $this->iCalendar;
	}
	
	/**
	 * @param WebexXmlSiteMyWebExPageType $myWebExDefaultPage
	 */
	public function setMyWebExDefaultPage(WebexXmlSiteMyWebExPageType $myWebExDefaultPage)
	{
		$this->myWebExDefaultPage = $myWebExDefaultPage;
	}
	
	/**
	 * @return WebexXmlSiteMyWebExPageType $myWebExDefaultPage
	 */
	public function getMyWebExDefaultPage()
	{
		return $this->myWebExDefaultPage;
	}
	
	/**
	 * @param string $componentVersion
	 */
	public function setComponentVersion($componentVersion)
	{
		$this->componentVersion = $componentVersion;
	}
	
	/**
	 * @return string $componentVersion
	 */
	public function getComponentVersion()
	{
		return $this->componentVersion;
	}
	
	/**
	 * @param long $accountNumLimit
	 */
	public function setAccountNumLimit($accountNumLimit)
	{
		$this->accountNumLimit = $accountNumLimit;
	}
	
	/**
	 * @return long $accountNumLimit
	 */
	public function getAccountNumLimit()
	{
		return $this->accountNumLimit;
	}
	
	/**
	 * @param long $activeUserCount
	 */
	public function setActiveUserCount($activeUserCount)
	{
		$this->activeUserCount = $activeUserCount;
	}
	
	/**
	 * @return long $activeUserCount
	 */
	public function getActiveUserCount()
	{
		return $this->activeUserCount;
	}
	
	/**
	 * @param long $auoAccountNumLimit
	 */
	public function setAuoAccountNumLimit($auoAccountNumLimit)
	{
		$this->auoAccountNumLimit = $auoAccountNumLimit;
	}
	
	/**
	 * @return long $auoAccountNumLimit
	 */
	public function getAuoAccountNumLimit()
	{
		return $this->auoAccountNumLimit;
	}
	
	/**
	 * @param long $auoActiveUserCount
	 */
	public function setAuoActiveUserCount($auoActiveUserCount)
	{
		$this->auoActiveUserCount = $auoActiveUserCount;
	}
	
	/**
	 * @return long $auoActiveUserCount
	 */
	public function getAuoActiveUserCount()
	{
		return $this->auoActiveUserCount;
	}
	
	/**
	 * @param boolean $displayMeetingActualTime
	 */
	public function setDisplayMeetingActualTime($displayMeetingActualTime)
	{
		$this->displayMeetingActualTime = $displayMeetingActualTime;
	}
	
	/**
	 * @return boolean $displayMeetingActualTime
	 */
	public function getDisplayMeetingActualTime()
	{
		return $this->displayMeetingActualTime;
	}
	
	/**
	 * @param boolean $displayOffset
	 */
	public function setDisplayOffset($displayOffset)
	{
		$this->displayOffset = $displayOffset;
	}
	
	/**
	 * @return boolean $displayOffset
	 */
	public function getDisplayOffset()
	{
		return $this->displayOffset;
	}
	
	/**
	 * @param boolean $supportWebEx11
	 */
	public function setSupportWebEx11($supportWebEx11)
	{
		$this->supportWebEx11 = $supportWebEx11;
	}
	
	/**
	 * @return boolean $supportWebEx11
	 */
	public function getSupportWebEx11()
	{
		return $this->supportWebEx11;
	}
	
}
		
