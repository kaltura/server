<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlAudioOnlyInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlAuoAccessControlType.class.php');
require_once(__DIR__ . '/WebexXmlAuoMetaDataType.class.php');
require_once(__DIR__ . '/WebexXmlAuoScheduleType.class.php');
require_once(__DIR__ . '/WebexXmlAuoTeleconfInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlComTrackingType.class.php');
require_once(__DIR__ . '/WebexXmlAuoRepeatType.class.php');
require_once(__DIR__ . '/WebexXmlAuoRemindType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlAuoAttendeeType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlAuoAttendeeType.class.php');
require_once(__DIR__ . '/WebexXmlAuoAttendeeOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlAuoTelephonyType.class.php');

class WebexXmlAudioOnlyInstanceTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlAuoAccessControlType
	 */
	protected $accessControl;
	
	/**
	 *
	 * @var WebexXmlAuoMetaDataType
	 */
	protected $metaData;
	
	/**
	 *
	 * @var WebexXmlAuoScheduleType
	 */
	protected $schedule;
	
	/**
	 *
	 * @var WebexXmlAuoTeleconfInstanceType
	 */
	protected $teleconference;
	
	/**
	 *
	 * @var WebexXmlComTrackingType
	 */
	protected $tracking;
	
	/**
	 *
	 * @var WebexXmlAuoRepeatType
	 */
	protected $repeat;
	
	/**
	 *
	 * @var WebexXmlAuoRemindType
	 */
	protected $remind;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlAuoAttendeeType>
	 */
	protected $fullAccessAttendees;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlAuoAttendeeType>
	 */
	protected $limitedAccessAttendees;
	
	/**
	 *
	 * @var WebexXmlAuoAttendeeOptionsType
	 */
	protected $attendeeOptions;
	
	/**
	 *
	 * @var WebexXmlAuoTelephonyType
	 */
	protected $telephony;
	
	/**
	 *
	 * @var string
	 */
	protected $hostType;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'accessControl',
			'metaData',
			'schedule',
			'teleconference',
			'tracking',
			'repeat',
			'remind',
			'fullAccessAttendees',
			'limitedAccessAttendees',
			'attendeeOptions',
			'telephony',
			'hostType',
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
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'teleconferenceonly';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'teleconferenceonly:audioOnlyInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlAudioOnlyInstanceType';
	}
	
	/**
	 * @param WebexXmlAuoAccessControlType $accessControl
	 */
	public function setAccessControl(WebexXmlAuoAccessControlType $accessControl)
	{
		$this->accessControl = $accessControl;
	}
	
	/**
	 * @param WebexXmlAuoMetaDataType $metaData
	 */
	public function setMetaData(WebexXmlAuoMetaDataType $metaData)
	{
		$this->metaData = $metaData;
	}
	
	/**
	 * @param WebexXmlAuoScheduleType $schedule
	 */
	public function setSchedule(WebexXmlAuoScheduleType $schedule)
	{
		$this->schedule = $schedule;
	}
	
	/**
	 * @param WebexXmlAuoTeleconfInstanceType $teleconference
	 */
	public function setTeleconference(WebexXmlAuoTeleconfInstanceType $teleconference)
	{
		$this->teleconference = $teleconference;
	}
	
	/**
	 * @param WebexXmlComTrackingType $tracking
	 */
	public function setTracking(WebexXmlComTrackingType $tracking)
	{
		$this->tracking = $tracking;
	}
	
	/**
	 * @param WebexXmlAuoRepeatType $repeat
	 */
	public function setRepeat(WebexXmlAuoRepeatType $repeat)
	{
		$this->repeat = $repeat;
	}
	
	/**
	 * @param WebexXmlAuoRemindType $remind
	 */
	public function setRemind(WebexXmlAuoRemindType $remind)
	{
		$this->remind = $remind;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlAuoAttendeeType> $fullAccessAttendees
	 */
	public function setFullAccessAttendees(WebexXmlArray $fullAccessAttendees)
	{
		if($fullAccessAttendees->getType() != 'WebexXmlAuoAttendeeType')
			throw new WebexXmlException(get_class($this) . "::fullAccessAttendees must be of type WebexXmlAuoAttendeeType");
		
		$this->fullAccessAttendees = $fullAccessAttendees;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlAuoAttendeeType> $limitedAccessAttendees
	 */
	public function setLimitedAccessAttendees(WebexXmlArray $limitedAccessAttendees)
	{
		if($limitedAccessAttendees->getType() != 'WebexXmlAuoAttendeeType')
			throw new WebexXmlException(get_class($this) . "::limitedAccessAttendees must be of type WebexXmlAuoAttendeeType");
		
		$this->limitedAccessAttendees = $limitedAccessAttendees;
	}
	
	/**
	 * @param WebexXmlAuoAttendeeOptionsType $attendeeOptions
	 */
	public function setAttendeeOptions(WebexXmlAuoAttendeeOptionsType $attendeeOptions)
	{
		$this->attendeeOptions = $attendeeOptions;
	}
	
	/**
	 * @param WebexXmlAuoTelephonyType $telephony
	 */
	public function setTelephony(WebexXmlAuoTelephonyType $telephony)
	{
		$this->telephony = $telephony;
	}
	
	/**
	 * @param string $hostType
	 */
	public function setHostType($hostType)
	{
		$this->hostType = $hostType;
	}
	
}
		
