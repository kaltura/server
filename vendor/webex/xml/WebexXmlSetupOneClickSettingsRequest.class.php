<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSetupOneClickSettings.class.php');
require_once(__DIR__ . '/WebexXmlEpOcMetaDataType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlEpOcTrackingCodeType.class.php');
require_once(__DIR__ . '/WebexXmlEpOcTelephonyType.class.php');
require_once(__DIR__ . '/WebexXmlEpOneClickEnableOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlEpAttendeeOptionsType.class.php');

class WebexXmlSetupOneClickSettingsRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlEpOcMetaDataType
	 */
	protected $metaData;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEpOcTrackingCodeType>
	 */
	protected $tracking;
	
	/**
	 *
	 * @var WebexXmlEpOcTelephonyType
	 */
	protected $telephony;
	
	/**
	 *
	 * @var WebexXmlEpOneClickEnableOptionsType
	 */
	protected $enableOptions;
	
	/**
	 *
	 * @var WebexXmlEpAttendeeOptionsType
	 */
	protected $attendeeOptions;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'metaData',
			'tracking',
			'telephony',
			'enableOptions',
			'attendeeOptions',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'metaData',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'ep';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'ep:setupOneClickSettings';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSetupOneClickSettings';
	}
	
	/**
	 * @param WebexXmlEpOcMetaDataType $metaData
	 */
	public function setMetaData(WebexXmlEpOcMetaDataType $metaData)
	{
		$this->metaData = $metaData;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlEpOcTrackingCodeType> $tracking
	 */
	public function setTracking(WebexXmlArray $tracking)
	{
		if($tracking->getType() != 'WebexXmlEpOcTrackingCodeType')
			throw new WebexXmlException(get_class($this) . "::tracking must be of type WebexXmlEpOcTrackingCodeType");
		
		$this->tracking = $tracking;
	}
	
	/**
	 * @param WebexXmlEpOcTelephonyType $telephony
	 */
	public function setTelephony(WebexXmlEpOcTelephonyType $telephony)
	{
		$this->telephony = $telephony;
	}
	
	/**
	 * @param WebexXmlEpOneClickEnableOptionsType $enableOptions
	 */
	public function setEnableOptions(WebexXmlEpOneClickEnableOptionsType $enableOptions)
	{
		$this->enableOptions = $enableOptions;
	}
	
	/**
	 * @param WebexXmlEpAttendeeOptionsType $attendeeOptions
	 */
	public function setAttendeeOptions(WebexXmlEpAttendeeOptionsType $attendeeOptions)
	{
		$this->attendeeOptions = $attendeeOptions;
	}
	
}
		
