<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlMeetingType.class.php');
require_once(__DIR__ . '/WebexXmlMeetAccessControlType.class.php');
require_once(__DIR__ . '/WebexXmlMeetMetaDataType.class.php');
require_once(__DIR__ . '/WebexXmlMeetParticipantsType.class.php');
require_once(__DIR__ . '/WebexXmlMeetEnableOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlMeetScheduleType.class.php');
require_once(__DIR__ . '/WebexXmlMeetTelephonyType.class.php');
require_once(__DIR__ . '/WebexXmlComTrackingType.class.php');
require_once(__DIR__ . '/WebexXmlMeetRepeatType.class.php');
require_once(__DIR__ . '/WebexXmlMeetRemindType.class.php');
require_once(__DIR__ . '/WebexXmlMeetAttendeeOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlServMeetingAssistType.class.php');

class WebexXmlMeetingTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlMeetAccessControlType
	 */
	protected $accessControl;
	
	/**
	 *
	 * @var WebexXmlMeetMetaDataType
	 */
	protected $metaData;
	
	/**
	 *
	 * @var WebexXmlMeetParticipantsType
	 */
	protected $participants;
	
	/**
	 *
	 * @var WebexXmlMeetEnableOptionsType
	 */
	protected $enableOptions;
	
	/**
	 *
	 * @var WebexXmlMeetScheduleType
	 */
	protected $schedule;
	
	/**
	 *
	 * @var WebexXmlMeetTelephonyType
	 */
	protected $telephony;
	
	/**
	 *
	 * @var WebexXmlComTrackingType
	 */
	protected $tracking;
	
	/**
	 *
	 * @var WebexXmlMeetRepeatType
	 */
	protected $repeat;
	
	/**
	 *
	 * @var WebexXmlMeetRemindType
	 */
	protected $remind;
	
	/**
	 *
	 * @var WebexXmlMeetAttendeeOptionsType
	 */
	protected $attendeeOptions;
	
	/**
	 *
	 * @var WebexXmlServMeetingAssistType
	 */
	protected $assistService;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'accessControl',
			'metaData',
			'participants',
			'enableOptions',
			'schedule',
			'telephony',
			'tracking',
			'repeat',
			'remind',
			'attendeeOptions',
			'assistService',
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
		return 'meeting';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'meeting:meetingType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlMeetingType';
	}
	
	/**
	 * @param WebexXmlMeetAccessControlType $accessControl
	 */
	public function setAccessControl(WebexXmlMeetAccessControlType $accessControl)
	{
		$this->accessControl = $accessControl;
	}
	
	/**
	 * @param WebexXmlMeetMetaDataType $metaData
	 */
	public function setMetaData(WebexXmlMeetMetaDataType $metaData)
	{
		$this->metaData = $metaData;
	}
	
	/**
	 * @param WebexXmlMeetParticipantsType $participants
	 */
	public function setParticipants(WebexXmlMeetParticipantsType $participants)
	{
		$this->participants = $participants;
	}
	
	/**
	 * @param WebexXmlMeetEnableOptionsType $enableOptions
	 */
	public function setEnableOptions(WebexXmlMeetEnableOptionsType $enableOptions)
	{
		$this->enableOptions = $enableOptions;
	}
	
	/**
	 * @param WebexXmlMeetScheduleType $schedule
	 */
	public function setSchedule(WebexXmlMeetScheduleType $schedule)
	{
		$this->schedule = $schedule;
	}
	
	/**
	 * @param WebexXmlMeetTelephonyType $telephony
	 */
	public function setTelephony(WebexXmlMeetTelephonyType $telephony)
	{
		$this->telephony = $telephony;
	}
	
	/**
	 * @param WebexXmlComTrackingType $tracking
	 */
	public function setTracking(WebexXmlComTrackingType $tracking)
	{
		$this->tracking = $tracking;
	}
	
	/**
	 * @param WebexXmlMeetRepeatType $repeat
	 */
	public function setRepeat(WebexXmlMeetRepeatType $repeat)
	{
		$this->repeat = $repeat;
	}
	
	/**
	 * @param WebexXmlMeetRemindType $remind
	 */
	public function setRemind(WebexXmlMeetRemindType $remind)
	{
		$this->remind = $remind;
	}
	
	/**
	 * @param WebexXmlMeetAttendeeOptionsType $attendeeOptions
	 */
	public function setAttendeeOptions(WebexXmlMeetAttendeeOptionsType $attendeeOptions)
	{
		$this->attendeeOptions = $attendeeOptions;
	}
	
	/**
	 * @param WebexXmlServMeetingAssistType $assistService
	 */
	public function setAssistService(WebexXmlServMeetingAssistType $assistService)
	{
		$this->assistService = $assistService;
	}
	
}

