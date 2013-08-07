<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteTelephonyConfigType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $isTSPUsingTelephonyAPI;
	
	/**
	 *
	 * @var string
	 */
	protected $serviceName;
	
	/**
	 *
	 * @var string
	 */
	protected $participantAccessCodeLabel;
	
	/**
	 *
	 * @var string
	 */
	protected $subscriberAccessCodeLabel;
	
	/**
	 *
	 * @var string
	 */
	protected $attendeeIDLabel;
	
	/**
	 *
	 * @var string
	 */
	protected $primaryAdaptorURL;
	
	/**
	 *
	 * @var string
	 */
	protected $secondaryAdaptorURL;
	
	/**
	 *
	 * @var boolean
	 */
	protected $internetPhone;
	
	/**
	 *
	 * @var boolean
	 */
	protected $callInTeleconferencing;
	
	/**
	 *
	 * @var boolean
	 */
	protected $tollFreeCallinTeleconferencing;
	
	/**
	 *
	 * @var boolean
	 */
	protected $callBackTeleconferencing;
	
	/**
	 *
	 * @var string
	 */
	protected $callInNumber;
	
	/**
	 *
	 * @var string
	 */
	protected $defaultTeleServerSubject;
	
	/**
	 *
	 * @var string
	 */
	protected $subscribeName;
	
	/**
	 *
	 * @var string
	 */
	protected $subscribePassword;
	
	/**
	 *
	 * @var string
	 */
	protected $defaultPhoneLines;
	
	/**
	 *
	 * @var string
	 */
	protected $defaultSpeakingLines;
	
	/**
	 *
	 * @var string
	 */
	protected $majorCountryCode;
	
	/**
	 *
	 * @var string
	 */
	protected $majorAreaCode;
	
	/**
	 *
	 * @var string
	 */
	protected $publicName;
	
	/**
	 *
	 * @var boolean
	 */
	protected $hybridTeleconference;
	
	/**
	 *
	 * @var boolean
	 */
	protected $instantHelp;
	
	/**
	 *
	 * @var boolean
	 */
	protected $customerManage;
	
	/**
	 *
	 * @var WebexXmlSiteTSRouting
	 */
	protected $TSRouting;
	
	/**
	 *
	 * @var string
	 */
	protected $teleServerName;
	
	/**
	 *
	 * @var long
	 */
	protected $maxCallersNumber;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isSpecified;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isContinue;
	
	/**
	 *
	 * @var boolean
	 */
	protected $intlCallBackTeleconferencing;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $personalTeleconf;
	
	/**
	 *
	 * @var boolean
	 */
	protected $multiMediaPlatform;
	
	/**
	 *
	 * @var string
	 */
	protected $multiMediaHostName;
	
	/**
	 *
	 * @var boolean
	 */
	protected $broadcastAudioStream;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $tspAdaptorSettings;
	
	/**
	 *
	 * @var WebexXmlSiteMeetingPlaceTelephonyType
	 */
	protected $meetingPlace;
	
	/**
	 *
	 * @var string
	 */
	protected $otherTeleServiceName;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportAdapterlessTSP;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayAttendeeID;
	
	/**
	 *
	 * @var boolean
	 */
	protected $provisionTeleAccount;
	
	/**
	 *
	 * @var boolean
	 */
	protected $choosePCN;
	
	/**
	 *
	 * @var boolean
	 */
	protected $audioOnly;
	
	/**
	 *
	 * @var boolean
	 */
	protected $configTollAndTollFreeNum;
	
	/**
	 *
	 * @var boolean
	 */
	protected $configPrimaryTS;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'isTSPUsingTelephonyAPI':
				return 'boolean';
	
			case 'serviceName':
				return 'string';
	
			case 'participantAccessCodeLabel':
				return 'string';
	
			case 'subscriberAccessCodeLabel':
				return 'string';
	
			case 'attendeeIDLabel':
				return 'string';
	
			case 'primaryAdaptorURL':
				return 'string';
	
			case 'secondaryAdaptorURL':
				return 'string';
	
			case 'internetPhone':
				return 'boolean';
	
			case 'callInTeleconferencing':
				return 'boolean';
	
			case 'tollFreeCallinTeleconferencing':
				return 'boolean';
	
			case 'callBackTeleconferencing':
				return 'boolean';
	
			case 'callInNumber':
				return 'string';
	
			case 'defaultTeleServerSubject':
				return 'string';
	
			case 'subscribeName':
				return 'string';
	
			case 'subscribePassword':
				return 'string';
	
			case 'defaultPhoneLines':
				return 'string';
	
			case 'defaultSpeakingLines':
				return 'string';
	
			case 'majorCountryCode':
				return 'string';
	
			case 'majorAreaCode':
				return 'string';
	
			case 'publicName':
				return 'string';
	
			case 'hybridTeleconference':
				return 'boolean';
	
			case 'instantHelp':
				return 'boolean';
	
			case 'customerManage':
				return 'boolean';
	
			case 'TSRouting':
				return 'WebexXmlSiteTSRouting';
	
			case 'teleServerName':
				return 'string';
	
			case 'maxCallersNumber':
				return 'long';
	
			case 'isSpecified':
				return 'boolean';
	
			case 'isContinue':
				return 'boolean';
	
			case 'intlCallBackTeleconferencing':
				return 'boolean';
	
			case 'personalTeleconf':
				return 'WebexXml';
	
			case 'multiMediaPlatform':
				return 'boolean';
	
			case 'multiMediaHostName':
				return 'string';
	
			case 'broadcastAudioStream':
				return 'boolean';
	
			case 'tspAdaptorSettings':
				return 'WebexXml';
	
			case 'meetingPlace':
				return 'WebexXmlSiteMeetingPlaceTelephonyType';
	
			case 'otherTeleServiceName':
				return 'string';
	
			case 'supportAdapterlessTSP':
				return 'boolean';
	
			case 'displayAttendeeID':
				return 'boolean';
	
			case 'provisionTeleAccount':
				return 'boolean';
	
			case 'choosePCN':
				return 'boolean';
	
			case 'audioOnly':
				return 'boolean';
	
			case 'configTollAndTollFreeNum':
				return 'boolean';
	
			case 'configPrimaryTS':
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
			'isTSPUsingTelephonyAPI',
			'serviceName',
			'participantAccessCodeLabel',
			'subscriberAccessCodeLabel',
			'attendeeIDLabel',
			'primaryAdaptorURL',
			'secondaryAdaptorURL',
			'internetPhone',
			'callInTeleconferencing',
			'tollFreeCallinTeleconferencing',
			'callBackTeleconferencing',
			'callInNumber',
			'defaultTeleServerSubject',
			'subscribeName',
			'subscribePassword',
			'defaultPhoneLines',
			'defaultSpeakingLines',
			'majorCountryCode',
			'majorAreaCode',
			'publicName',
			'hybridTeleconference',
			'instantHelp',
			'customerManage',
			'TSRouting',
			'teleServerName',
			'maxCallersNumber',
			'isSpecified',
			'isContinue',
			'intlCallBackTeleconferencing',
			'personalTeleconf',
			'multiMediaPlatform',
			'multiMediaHostName',
			'broadcastAudioStream',
			'tspAdaptorSettings',
			'meetingPlace',
			'otherTeleServiceName',
			'supportAdapterlessTSP',
			'displayAttendeeID',
			'provisionTeleAccount',
			'choosePCN',
			'audioOnly',
			'configTollAndTollFreeNum',
			'configPrimaryTS',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'tspAdaptorSettings',
			'meetingPlace',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'telephonyConfigType';
	}
	
	/**
	 * @param boolean $isTSPUsingTelephonyAPI
	 */
	public function setIsTSPUsingTelephonyAPI($isTSPUsingTelephonyAPI)
	{
		$this->isTSPUsingTelephonyAPI = $isTSPUsingTelephonyAPI;
	}
	
	/**
	 * @return boolean $isTSPUsingTelephonyAPI
	 */
	public function getIsTSPUsingTelephonyAPI()
	{
		return $this->isTSPUsingTelephonyAPI;
	}
	
	/**
	 * @param string $serviceName
	 */
	public function setServiceName($serviceName)
	{
		$this->serviceName = $serviceName;
	}
	
	/**
	 * @return string $serviceName
	 */
	public function getServiceName()
	{
		return $this->serviceName;
	}
	
	/**
	 * @param string $participantAccessCodeLabel
	 */
	public function setParticipantAccessCodeLabel($participantAccessCodeLabel)
	{
		$this->participantAccessCodeLabel = $participantAccessCodeLabel;
	}
	
	/**
	 * @return string $participantAccessCodeLabel
	 */
	public function getParticipantAccessCodeLabel()
	{
		return $this->participantAccessCodeLabel;
	}
	
	/**
	 * @param string $subscriberAccessCodeLabel
	 */
	public function setSubscriberAccessCodeLabel($subscriberAccessCodeLabel)
	{
		$this->subscriberAccessCodeLabel = $subscriberAccessCodeLabel;
	}
	
	/**
	 * @return string $subscriberAccessCodeLabel
	 */
	public function getSubscriberAccessCodeLabel()
	{
		return $this->subscriberAccessCodeLabel;
	}
	
	/**
	 * @param string $attendeeIDLabel
	 */
	public function setAttendeeIDLabel($attendeeIDLabel)
	{
		$this->attendeeIDLabel = $attendeeIDLabel;
	}
	
	/**
	 * @return string $attendeeIDLabel
	 */
	public function getAttendeeIDLabel()
	{
		return $this->attendeeIDLabel;
	}
	
	/**
	 * @param string $primaryAdaptorURL
	 */
	public function setPrimaryAdaptorURL($primaryAdaptorURL)
	{
		$this->primaryAdaptorURL = $primaryAdaptorURL;
	}
	
	/**
	 * @return string $primaryAdaptorURL
	 */
	public function getPrimaryAdaptorURL()
	{
		return $this->primaryAdaptorURL;
	}
	
	/**
	 * @param string $secondaryAdaptorURL
	 */
	public function setSecondaryAdaptorURL($secondaryAdaptorURL)
	{
		$this->secondaryAdaptorURL = $secondaryAdaptorURL;
	}
	
	/**
	 * @return string $secondaryAdaptorURL
	 */
	public function getSecondaryAdaptorURL()
	{
		return $this->secondaryAdaptorURL;
	}
	
	/**
	 * @param boolean $internetPhone
	 */
	public function setInternetPhone($internetPhone)
	{
		$this->internetPhone = $internetPhone;
	}
	
	/**
	 * @return boolean $internetPhone
	 */
	public function getInternetPhone()
	{
		return $this->internetPhone;
	}
	
	/**
	 * @param boolean $callInTeleconferencing
	 */
	public function setCallInTeleconferencing($callInTeleconferencing)
	{
		$this->callInTeleconferencing = $callInTeleconferencing;
	}
	
	/**
	 * @return boolean $callInTeleconferencing
	 */
	public function getCallInTeleconferencing()
	{
		return $this->callInTeleconferencing;
	}
	
	/**
	 * @param boolean $tollFreeCallinTeleconferencing
	 */
	public function setTollFreeCallinTeleconferencing($tollFreeCallinTeleconferencing)
	{
		$this->tollFreeCallinTeleconferencing = $tollFreeCallinTeleconferencing;
	}
	
	/**
	 * @return boolean $tollFreeCallinTeleconferencing
	 */
	public function getTollFreeCallinTeleconferencing()
	{
		return $this->tollFreeCallinTeleconferencing;
	}
	
	/**
	 * @param boolean $callBackTeleconferencing
	 */
	public function setCallBackTeleconferencing($callBackTeleconferencing)
	{
		$this->callBackTeleconferencing = $callBackTeleconferencing;
	}
	
	/**
	 * @return boolean $callBackTeleconferencing
	 */
	public function getCallBackTeleconferencing()
	{
		return $this->callBackTeleconferencing;
	}
	
	/**
	 * @param string $callInNumber
	 */
	public function setCallInNumber($callInNumber)
	{
		$this->callInNumber = $callInNumber;
	}
	
	/**
	 * @return string $callInNumber
	 */
	public function getCallInNumber()
	{
		return $this->callInNumber;
	}
	
	/**
	 * @param string $defaultTeleServerSubject
	 */
	public function setDefaultTeleServerSubject($defaultTeleServerSubject)
	{
		$this->defaultTeleServerSubject = $defaultTeleServerSubject;
	}
	
	/**
	 * @return string $defaultTeleServerSubject
	 */
	public function getDefaultTeleServerSubject()
	{
		return $this->defaultTeleServerSubject;
	}
	
	/**
	 * @param string $subscribeName
	 */
	public function setSubscribeName($subscribeName)
	{
		$this->subscribeName = $subscribeName;
	}
	
	/**
	 * @return string $subscribeName
	 */
	public function getSubscribeName()
	{
		return $this->subscribeName;
	}
	
	/**
	 * @param string $subscribePassword
	 */
	public function setSubscribePassword($subscribePassword)
	{
		$this->subscribePassword = $subscribePassword;
	}
	
	/**
	 * @return string $subscribePassword
	 */
	public function getSubscribePassword()
	{
		return $this->subscribePassword;
	}
	
	/**
	 * @param string $defaultPhoneLines
	 */
	public function setDefaultPhoneLines($defaultPhoneLines)
	{
		$this->defaultPhoneLines = $defaultPhoneLines;
	}
	
	/**
	 * @return string $defaultPhoneLines
	 */
	public function getDefaultPhoneLines()
	{
		return $this->defaultPhoneLines;
	}
	
	/**
	 * @param string $defaultSpeakingLines
	 */
	public function setDefaultSpeakingLines($defaultSpeakingLines)
	{
		$this->defaultSpeakingLines = $defaultSpeakingLines;
	}
	
	/**
	 * @return string $defaultSpeakingLines
	 */
	public function getDefaultSpeakingLines()
	{
		return $this->defaultSpeakingLines;
	}
	
	/**
	 * @param string $majorCountryCode
	 */
	public function setMajorCountryCode($majorCountryCode)
	{
		$this->majorCountryCode = $majorCountryCode;
	}
	
	/**
	 * @return string $majorCountryCode
	 */
	public function getMajorCountryCode()
	{
		return $this->majorCountryCode;
	}
	
	/**
	 * @param string $majorAreaCode
	 */
	public function setMajorAreaCode($majorAreaCode)
	{
		$this->majorAreaCode = $majorAreaCode;
	}
	
	/**
	 * @return string $majorAreaCode
	 */
	public function getMajorAreaCode()
	{
		return $this->majorAreaCode;
	}
	
	/**
	 * @param string $publicName
	 */
	public function setPublicName($publicName)
	{
		$this->publicName = $publicName;
	}
	
	/**
	 * @return string $publicName
	 */
	public function getPublicName()
	{
		return $this->publicName;
	}
	
	/**
	 * @param boolean $hybridTeleconference
	 */
	public function setHybridTeleconference($hybridTeleconference)
	{
		$this->hybridTeleconference = $hybridTeleconference;
	}
	
	/**
	 * @return boolean $hybridTeleconference
	 */
	public function getHybridTeleconference()
	{
		return $this->hybridTeleconference;
	}
	
	/**
	 * @param boolean $instantHelp
	 */
	public function setInstantHelp($instantHelp)
	{
		$this->instantHelp = $instantHelp;
	}
	
	/**
	 * @return boolean $instantHelp
	 */
	public function getInstantHelp()
	{
		return $this->instantHelp;
	}
	
	/**
	 * @param boolean $customerManage
	 */
	public function setCustomerManage($customerManage)
	{
		$this->customerManage = $customerManage;
	}
	
	/**
	 * @return boolean $customerManage
	 */
	public function getCustomerManage()
	{
		return $this->customerManage;
	}
	
	/**
	 * @param WebexXmlSiteTSRouting $TSRouting
	 */
	public function setTSRouting(WebexXmlSiteTSRouting $TSRouting)
	{
		$this->TSRouting = $TSRouting;
	}
	
	/**
	 * @return WebexXmlSiteTSRouting $TSRouting
	 */
	public function getTSRouting()
	{
		return $this->TSRouting;
	}
	
	/**
	 * @param string $teleServerName
	 */
	public function setTeleServerName($teleServerName)
	{
		$this->teleServerName = $teleServerName;
	}
	
	/**
	 * @return string $teleServerName
	 */
	public function getTeleServerName()
	{
		return $this->teleServerName;
	}
	
	/**
	 * @param long $maxCallersNumber
	 */
	public function setMaxCallersNumber($maxCallersNumber)
	{
		$this->maxCallersNumber = $maxCallersNumber;
	}
	
	/**
	 * @return long $maxCallersNumber
	 */
	public function getMaxCallersNumber()
	{
		return $this->maxCallersNumber;
	}
	
	/**
	 * @param boolean $isSpecified
	 */
	public function setIsSpecified($isSpecified)
	{
		$this->isSpecified = $isSpecified;
	}
	
	/**
	 * @return boolean $isSpecified
	 */
	public function getIsSpecified()
	{
		return $this->isSpecified;
	}
	
	/**
	 * @param boolean $isContinue
	 */
	public function setIsContinue($isContinue)
	{
		$this->isContinue = $isContinue;
	}
	
	/**
	 * @return boolean $isContinue
	 */
	public function getIsContinue()
	{
		return $this->isContinue;
	}
	
	/**
	 * @param boolean $intlCallBackTeleconferencing
	 */
	public function setIntlCallBackTeleconferencing($intlCallBackTeleconferencing)
	{
		$this->intlCallBackTeleconferencing = $intlCallBackTeleconferencing;
	}
	
	/**
	 * @return boolean $intlCallBackTeleconferencing
	 */
	public function getIntlCallBackTeleconferencing()
	{
		return $this->intlCallBackTeleconferencing;
	}
	
	/**
	 * @param WebexXml $personalTeleconf
	 */
	public function setPersonalTeleconf(WebexXml $personalTeleconf)
	{
		$this->personalTeleconf = $personalTeleconf;
	}
	
	/**
	 * @return WebexXml $personalTeleconf
	 */
	public function getPersonalTeleconf()
	{
		return $this->personalTeleconf;
	}
	
	/**
	 * @param boolean $multiMediaPlatform
	 */
	public function setMultiMediaPlatform($multiMediaPlatform)
	{
		$this->multiMediaPlatform = $multiMediaPlatform;
	}
	
	/**
	 * @return boolean $multiMediaPlatform
	 */
	public function getMultiMediaPlatform()
	{
		return $this->multiMediaPlatform;
	}
	
	/**
	 * @param string $multiMediaHostName
	 */
	public function setMultiMediaHostName($multiMediaHostName)
	{
		$this->multiMediaHostName = $multiMediaHostName;
	}
	
	/**
	 * @return string $multiMediaHostName
	 */
	public function getMultiMediaHostName()
	{
		return $this->multiMediaHostName;
	}
	
	/**
	 * @param boolean $broadcastAudioStream
	 */
	public function setBroadcastAudioStream($broadcastAudioStream)
	{
		$this->broadcastAudioStream = $broadcastAudioStream;
	}
	
	/**
	 * @return boolean $broadcastAudioStream
	 */
	public function getBroadcastAudioStream()
	{
		return $this->broadcastAudioStream;
	}
	
	/**
	 * @param WebexXml $tspAdaptorSettings
	 */
	public function setTspAdaptorSettings(WebexXml $tspAdaptorSettings)
	{
		$this->tspAdaptorSettings = $tspAdaptorSettings;
	}
	
	/**
	 * @return WebexXml $tspAdaptorSettings
	 */
	public function getTspAdaptorSettings()
	{
		return $this->tspAdaptorSettings;
	}
	
	/**
	 * @param WebexXmlSiteMeetingPlaceTelephonyType $meetingPlace
	 */
	public function setMeetingPlace(WebexXmlSiteMeetingPlaceTelephonyType $meetingPlace)
	{
		$this->meetingPlace = $meetingPlace;
	}
	
	/**
	 * @return WebexXmlSiteMeetingPlaceTelephonyType $meetingPlace
	 */
	public function getMeetingPlace()
	{
		return $this->meetingPlace;
	}
	
	/**
	 * @param string $otherTeleServiceName
	 */
	public function setOtherTeleServiceName($otherTeleServiceName)
	{
		$this->otherTeleServiceName = $otherTeleServiceName;
	}
	
	/**
	 * @return string $otherTeleServiceName
	 */
	public function getOtherTeleServiceName()
	{
		return $this->otherTeleServiceName;
	}
	
	/**
	 * @param boolean $supportAdapterlessTSP
	 */
	public function setSupportAdapterlessTSP($supportAdapterlessTSP)
	{
		$this->supportAdapterlessTSP = $supportAdapterlessTSP;
	}
	
	/**
	 * @return boolean $supportAdapterlessTSP
	 */
	public function getSupportAdapterlessTSP()
	{
		return $this->supportAdapterlessTSP;
	}
	
	/**
	 * @param boolean $displayAttendeeID
	 */
	public function setDisplayAttendeeID($displayAttendeeID)
	{
		$this->displayAttendeeID = $displayAttendeeID;
	}
	
	/**
	 * @return boolean $displayAttendeeID
	 */
	public function getDisplayAttendeeID()
	{
		return $this->displayAttendeeID;
	}
	
	/**
	 * @param boolean $provisionTeleAccount
	 */
	public function setProvisionTeleAccount($provisionTeleAccount)
	{
		$this->provisionTeleAccount = $provisionTeleAccount;
	}
	
	/**
	 * @return boolean $provisionTeleAccount
	 */
	public function getProvisionTeleAccount()
	{
		return $this->provisionTeleAccount;
	}
	
	/**
	 * @param boolean $choosePCN
	 */
	public function setChoosePCN($choosePCN)
	{
		$this->choosePCN = $choosePCN;
	}
	
	/**
	 * @return boolean $choosePCN
	 */
	public function getChoosePCN()
	{
		return $this->choosePCN;
	}
	
	/**
	 * @param boolean $audioOnly
	 */
	public function setAudioOnly($audioOnly)
	{
		$this->audioOnly = $audioOnly;
	}
	
	/**
	 * @return boolean $audioOnly
	 */
	public function getAudioOnly()
	{
		return $this->audioOnly;
	}
	
	/**
	 * @param boolean $configTollAndTollFreeNum
	 */
	public function setConfigTollAndTollFreeNum($configTollAndTollFreeNum)
	{
		$this->configTollAndTollFreeNum = $configTollAndTollFreeNum;
	}
	
	/**
	 * @return boolean $configTollAndTollFreeNum
	 */
	public function getConfigTollAndTollFreeNum()
	{
		return $this->configTollAndTollFreeNum;
	}
	
	/**
	 * @param boolean $configPrimaryTS
	 */
	public function setConfigPrimaryTS($configPrimaryTS)
	{
		$this->configPrimaryTS = $configPrimaryTS;
	}
	
	/**
	 * @return boolean $configPrimaryTS
	 */
	public function getConfigPrimaryTS()
	{
		return $this->configPrimaryTS;
	}
	
}
		
