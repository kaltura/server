<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSiteType.class.php');
require_once(__DIR__ . '/WebexXmlSiteMetaDataType.class.php');
require_once(__DIR__ . '/WebexXmlSiteUcfType.class.php');
require_once(__DIR__ . '/WebexXmlSiteClientPlatformsType.class.php');
require_once(__DIR__ . '/WebexXmlSiteResourceRestrictionsType.class.php');
require_once(__DIR__ . '/WebexXmlSiteSupportAPIType.class.php');
require_once(__DIR__ . '/WebexXmlSiteMyWebExConfigType.class.php');
require_once(__DIR__ . '/WebexXmlSiteTelephonyConfigType.class.php');
require_once(__DIR__ . '/WebexXmlSiteCommerceAndReportingType.class.php');
require_once(__DIR__ . '/WebexXmlSiteToolsType.class.php');
require_once(__DIR__ . '/WebexXmlSiteCustCommunicationsType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlSiteTrackingCodeType.class.php');
require_once(__DIR__ . '/WebexXmlSiteSupportedServicesType.class.php');
require_once(__DIR__ . '/WebexXmlSiteSecurityOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlSiteDefaultsType.class.php');
require_once(__DIR__ . '/WebexXmlSiteScheduleOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlSiteTopBarType.class.php');
require_once(__DIR__ . '/WebexXmlSiteMyWebExBarType.class.php');
require_once(__DIR__ . '/WebexXmlSiteAllServiceBarType.class.php');
require_once(__DIR__ . '/WebexXmlSitePasswordCriteriaType.class.php');
require_once(__DIR__ . '/WebexXmlSiteAccountPasswordCriteriaType.class.php');
require_once(__DIR__ . '/WebexXmlSiteProductivityToolType.class.php');
require_once(__DIR__ . '/WebexXmlSiteMeetingPlaceType.class.php');
require_once(__DIR__ . '/WebexXmlSiteEventCenterType.class.php');
require_once(__DIR__ . '/WebexXmlSiteSalesCenterType.class.php');
require_once(__DIR__ . '/WebexXmlSiteConnectIntegrationType.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlSiteTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlSiteMetaDataType
	 */
	protected $metaData;
	
	/**
	 *
	 * @var WebexXmlSiteUcfType
	 */
	protected $ucf;
	
	/**
	 *
	 * @var WebexXmlSiteClientPlatformsType
	 */
	protected $clientPlatforms;
	
	/**
	 *
	 * @var WebexXmlSiteResourceRestrictionsType
	 */
	protected $resourceRestrictions;
	
	/**
	 *
	 * @var WebexXmlSiteSupportAPIType
	 */
	protected $supportAPI;
	
	/**
	 *
	 * @var WebexXmlSiteMyWebExConfigType
	 */
	protected $myWebExConfig;
	
	/**
	 *
	 * @var WebexXmlSiteTelephonyConfigType
	 */
	protected $telephonyConfig;
	
	/**
	 *
	 * @var WebexXmlSiteCommerceAndReportingType
	 */
	protected $commerceAndReporting;
	
	/**
	 *
	 * @var WebexXmlSiteToolsType
	 */
	protected $tools;
	
	/**
	 *
	 * @var WebexXmlSiteCustCommunicationsType
	 */
	protected $custCommunications;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSiteTrackingCodeType>
	 */
	protected $trackingCodes;
	
	/**
	 *
	 * @var WebexXmlSiteSupportedServicesType
	 */
	protected $supportedServices;
	
	/**
	 *
	 * @var WebexXmlSiteSecurityOptionsType
	 */
	protected $securityOptions;
	
	/**
	 *
	 * @var WebexXmlSiteDefaultsType
	 */
	protected $defaults;
	
	/**
	 *
	 * @var WebexXmlSiteScheduleOptionsType
	 */
	protected $scheduleMeetingOptions;
	
	/**
	 *
	 * @var WebexXmlSiteTopBarType
	 */
	protected $navBarTop;
	
	/**
	 *
	 * @var WebexXmlSiteMyWebExBarType
	 */
	protected $navMyWebEx;
	
	/**
	 *
	 * @var WebexXmlSiteAllServiceBarType
	 */
	protected $navAllServices;
	
	/**
	 *
	 * @var WebexXmlSitePasswordCriteriaType
	 */
	protected $passwordCriteria;
	
	/**
	 *
	 * @var WebexXmlSiteAccountPasswordCriteriaType
	 */
	protected $accountPasswordCriteria;
	
	/**
	 *
	 * @var WebexXmlSiteProductivityToolType
	 */
	protected $productivityTools;
	
	/**
	 *
	 * @var WebexXmlSiteMeetingPlaceType
	 */
	protected $meetingPlace;
	
	/**
	 *
	 * @var WebexXmlSiteEventCenterType
	 */
	protected $eventCenter;
	
	/**
	 *
	 * @var WebexXmlSiteSalesCenterType
	 */
	protected $salesCenter;
	
	/**
	 *
	 * @var WebexXmlSiteConnectIntegrationType
	 */
	protected $connectIntegration;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $video;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $siteCommonOptions;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'metaData',
			'ucf',
			'clientPlatforms',
			'resourceRestrictions',
			'supportAPI',
			'myWebExConfig',
			'telephonyConfig',
			'commerceAndReporting',
			'tools',
			'custCommunications',
			'trackingCodes',
			'supportedServices',
			'securityOptions',
			'defaults',
			'scheduleMeetingOptions',
			'navBarTop',
			'navMyWebEx',
			'navAllServices',
			'passwordCriteria',
			'accountPasswordCriteria',
			'productivityTools',
			'meetingPlace',
			'eventCenter',
			'salesCenter',
			'connectIntegration',
			'video',
			'siteCommonOptions',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'ucf',
			'resourceRestrictions',
			'supportAPI',
			'passwordCriteria',
			'accountPasswordCriteria',
			'meetingPlace',
			'video',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'site';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'site:siteType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSiteType';
	}
	
	/**
	 * @param WebexXmlSiteMetaDataType $metaData
	 */
	public function setMetaData(WebexXmlSiteMetaDataType $metaData)
	{
		$this->metaData = $metaData;
	}
	
	/**
	 * @param WebexXmlSiteUcfType $ucf
	 */
	public function setUcf(WebexXmlSiteUcfType $ucf)
	{
		$this->ucf = $ucf;
	}
	
	/**
	 * @param WebexXmlSiteClientPlatformsType $clientPlatforms
	 */
	public function setClientPlatforms(WebexXmlSiteClientPlatformsType $clientPlatforms)
	{
		$this->clientPlatforms = $clientPlatforms;
	}
	
	/**
	 * @param WebexXmlSiteResourceRestrictionsType $resourceRestrictions
	 */
	public function setResourceRestrictions(WebexXmlSiteResourceRestrictionsType $resourceRestrictions)
	{
		$this->resourceRestrictions = $resourceRestrictions;
	}
	
	/**
	 * @param WebexXmlSiteSupportAPIType $supportAPI
	 */
	public function setSupportAPI(WebexXmlSiteSupportAPIType $supportAPI)
	{
		$this->supportAPI = $supportAPI;
	}
	
	/**
	 * @param WebexXmlSiteMyWebExConfigType $myWebExConfig
	 */
	public function setMyWebExConfig(WebexXmlSiteMyWebExConfigType $myWebExConfig)
	{
		$this->myWebExConfig = $myWebExConfig;
	}
	
	/**
	 * @param WebexXmlSiteTelephonyConfigType $telephonyConfig
	 */
	public function setTelephonyConfig(WebexXmlSiteTelephonyConfigType $telephonyConfig)
	{
		$this->telephonyConfig = $telephonyConfig;
	}
	
	/**
	 * @param WebexXmlSiteCommerceAndReportingType $commerceAndReporting
	 */
	public function setCommerceAndReporting(WebexXmlSiteCommerceAndReportingType $commerceAndReporting)
	{
		$this->commerceAndReporting = $commerceAndReporting;
	}
	
	/**
	 * @param WebexXmlSiteToolsType $tools
	 */
	public function setTools(WebexXmlSiteToolsType $tools)
	{
		$this->tools = $tools;
	}
	
	/**
	 * @param WebexXmlSiteCustCommunicationsType $custCommunications
	 */
	public function setCustCommunications(WebexXmlSiteCustCommunicationsType $custCommunications)
	{
		$this->custCommunications = $custCommunications;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlSiteTrackingCodeType> $trackingCodes
	 */
	public function setTrackingCodes(WebexXmlArray $trackingCodes)
	{
		if($trackingCodes->getType() != 'WebexXmlSiteTrackingCodeType')
			throw new WebexXmlException(get_class($this) . "::trackingCodes must be of type WebexXmlSiteTrackingCodeType");
		
		$this->trackingCodes = $trackingCodes;
	}
	
	/**
	 * @param WebexXmlSiteSupportedServicesType $supportedServices
	 */
	public function setSupportedServices(WebexXmlSiteSupportedServicesType $supportedServices)
	{
		$this->supportedServices = $supportedServices;
	}
	
	/**
	 * @param WebexXmlSiteSecurityOptionsType $securityOptions
	 */
	public function setSecurityOptions(WebexXmlSiteSecurityOptionsType $securityOptions)
	{
		$this->securityOptions = $securityOptions;
	}
	
	/**
	 * @param WebexXmlSiteDefaultsType $defaults
	 */
	public function setDefaults(WebexXmlSiteDefaultsType $defaults)
	{
		$this->defaults = $defaults;
	}
	
	/**
	 * @param WebexXmlSiteScheduleOptionsType $scheduleMeetingOptions
	 */
	public function setScheduleMeetingOptions(WebexXmlSiteScheduleOptionsType $scheduleMeetingOptions)
	{
		$this->scheduleMeetingOptions = $scheduleMeetingOptions;
	}
	
	/**
	 * @param WebexXmlSiteTopBarType $navBarTop
	 */
	public function setNavBarTop(WebexXmlSiteTopBarType $navBarTop)
	{
		$this->navBarTop = $navBarTop;
	}
	
	/**
	 * @param WebexXmlSiteMyWebExBarType $navMyWebEx
	 */
	public function setNavMyWebEx(WebexXmlSiteMyWebExBarType $navMyWebEx)
	{
		$this->navMyWebEx = $navMyWebEx;
	}
	
	/**
	 * @param WebexXmlSiteAllServiceBarType $navAllServices
	 */
	public function setNavAllServices(WebexXmlSiteAllServiceBarType $navAllServices)
	{
		$this->navAllServices = $navAllServices;
	}
	
	/**
	 * @param WebexXmlSitePasswordCriteriaType $passwordCriteria
	 */
	public function setPasswordCriteria(WebexXmlSitePasswordCriteriaType $passwordCriteria)
	{
		$this->passwordCriteria = $passwordCriteria;
	}
	
	/**
	 * @param WebexXmlSiteAccountPasswordCriteriaType $accountPasswordCriteria
	 */
	public function setAccountPasswordCriteria(WebexXmlSiteAccountPasswordCriteriaType $accountPasswordCriteria)
	{
		$this->accountPasswordCriteria = $accountPasswordCriteria;
	}
	
	/**
	 * @param WebexXmlSiteProductivityToolType $productivityTools
	 */
	public function setProductivityTools(WebexXmlSiteProductivityToolType $productivityTools)
	{
		$this->productivityTools = $productivityTools;
	}
	
	/**
	 * @param WebexXmlSiteMeetingPlaceType $meetingPlace
	 */
	public function setMeetingPlace(WebexXmlSiteMeetingPlaceType $meetingPlace)
	{
		$this->meetingPlace = $meetingPlace;
	}
	
	/**
	 * @param WebexXmlSiteEventCenterType $eventCenter
	 */
	public function setEventCenter(WebexXmlSiteEventCenterType $eventCenter)
	{
		$this->eventCenter = $eventCenter;
	}
	
	/**
	 * @param WebexXmlSiteSalesCenterType $salesCenter
	 */
	public function setSalesCenter(WebexXmlSiteSalesCenterType $salesCenter)
	{
		$this->salesCenter = $salesCenter;
	}
	
	/**
	 * @param WebexXmlSiteConnectIntegrationType $connectIntegration
	 */
	public function setConnectIntegration(WebexXmlSiteConnectIntegrationType $connectIntegration)
	{
		$this->connectIntegration = $connectIntegration;
	}
	
	/**
	 * @param WebexXml $video
	 */
	public function setVideo(WebexXml $video)
	{
		$this->video = $video;
	}
	
	/**
	 * @param WebexXml $siteCommonOptions
	 */
	public function setSiteCommonOptions(WebexXml $siteCommonOptions)
	{
		$this->siteCommonOptions = $siteCommonOptions;
	}
	
}
		
