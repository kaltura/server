<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlEpOcMetaDataType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlEpOneClickTrackingCodeType.class.php');
require_once(__DIR__ . '/WebexXmlEpOneClickTelephonyType.class.php');
require_once(__DIR__ . '/WebexXmlEpOneClickEnableOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlEpAttendeeOptionsType.class.php');

class WebexXmlGetOneClickSettings extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlEpOcMetaDataType
	 */
	protected $metaData;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEpOneClickTrackingCodeType>
	 */
	protected $trackingCodes;
	
	/**
	 *
	 * @var WebexXmlEpOneClickTelephonyType
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
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'metaData':
				return 'WebexXmlEpOcMetaDataType';
	
			case 'trackingCodes':
				return 'WebexXmlArray<WebexXmlEpOneClickTrackingCodeType>';
	
			case 'telephony':
				return 'WebexXmlEpOneClickTelephonyType';
	
			case 'enableOptions':
				return 'WebexXmlEpOneClickEnableOptionsType';
	
			case 'attendeeOptions':
				return 'WebexXmlEpAttendeeOptionsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlEpOcMetaDataType $metaData
	 */
	public function getMetaData()
	{
		return $this->metaData;
	}
	
	/**
	 * @return WebexXmlArray $trackingCodes
	 */
	public function getTrackingCodes()
	{
		return $this->trackingCodes;
	}
	
	/**
	 * @return WebexXmlEpOneClickTelephonyType $telephony
	 */
	public function getTelephony()
	{
		return $this->telephony;
	}
	
	/**
	 * @return WebexXmlEpOneClickEnableOptionsType $enableOptions
	 */
	public function getEnableOptions()
	{
		return $this->enableOptions;
	}
	
	/**
	 * @return WebexXmlEpAttendeeOptionsType $attendeeOptions
	 */
	public function getAttendeeOptions()
	{
		return $this->attendeeOptions;
	}
	
}
		
