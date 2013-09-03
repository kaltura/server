<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlEventType.class.php');
require_once(__DIR__ . '/WebexXmlEventAccessControlType.class.php');
require_once(__DIR__ . '/WebexXmlEventMetaDataType.class.php');
require_once(__DIR__ . '/WebexXmlEventScheduleType.class.php');
require_once(__DIR__ . '/WebexXmlEventTelephonyType.class.php');
require_once(__DIR__ . '/WebexXmlComTrackingType.class.php');
require_once(__DIR__ . '/WebexXmlEventRemindType.class.php');
require_once(__DIR__ . '/WebexXmlEventPanelistsType.class.php');
require_once(__DIR__ . '/WebexXmlEventAttendeesType.class.php');
require_once(__DIR__ . '/WebexXmlEventExtOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlEventEmailTemplatesType.class.php');
require_once(__DIR__ . '/WebexXmlServMeetingAssistType.class.php');

class WebexXmlEventTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlEventAccessControlType
	 */
	protected $accessControl;
	
	/**
	 *
	 * @var WebexXmlEventMetaDataType
	 */
	protected $metaData;
	
	/**
	 *
	 * @var WebexXmlEventScheduleType
	 */
	protected $schedule;
	
	/**
	 *
	 * @var WebexXmlEventTelephonyType
	 */
	protected $telephony;
	
	/**
	 *
	 * @var WebexXmlComTrackingType
	 */
	protected $tracking;
	
	/**
	 *
	 * @var WebexXmlEventRemindType
	 */
	protected $remind;
	
	/**
	 *
	 * @var WebexXmlEventPanelistsType
	 */
	protected $panelists;
	
	/**
	 *
	 * @var WebexXmlEventAttendeesType
	 */
	protected $attendees;
	
	/**
	 *
	 * @var WebexXmlEventExtOptionsType
	 */
	protected $extOptions;
	
	/**
	 *
	 * @var WebexXmlEventEmailTemplatesType
	 */
	protected $emailTemplates;
	
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
			'schedule',
			'telephony',
			'tracking',
			'remind',
			'panelists',
			'attendees',
			'extOptions',
			'emailTemplates',
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
		return 'event';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'event:eventType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlEventType';
	}
	
	/**
	 * @param WebexXmlEventAccessControlType $accessControl
	 */
	public function setAccessControl(WebexXmlEventAccessControlType $accessControl)
	{
		$this->accessControl = $accessControl;
	}
	
	/**
	 * @param WebexXmlEventMetaDataType $metaData
	 */
	public function setMetaData(WebexXmlEventMetaDataType $metaData)
	{
		$this->metaData = $metaData;
	}
	
	/**
	 * @param WebexXmlEventScheduleType $schedule
	 */
	public function setSchedule(WebexXmlEventScheduleType $schedule)
	{
		$this->schedule = $schedule;
	}
	
	/**
	 * @param WebexXmlEventTelephonyType $telephony
	 */
	public function setTelephony(WebexXmlEventTelephonyType $telephony)
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
	 * @param WebexXmlEventRemindType $remind
	 */
	public function setRemind(WebexXmlEventRemindType $remind)
	{
		$this->remind = $remind;
	}
	
	/**
	 * @param WebexXmlEventPanelistsType $panelists
	 */
	public function setPanelists(WebexXmlEventPanelistsType $panelists)
	{
		$this->panelists = $panelists;
	}
	
	/**
	 * @param WebexXmlEventAttendeesType $attendees
	 */
	public function setAttendees(WebexXmlEventAttendeesType $attendees)
	{
		$this->attendees = $attendees;
	}
	
	/**
	 * @param WebexXmlEventExtOptionsType $extOptions
	 */
	public function setExtOptions(WebexXmlEventExtOptionsType $extOptions)
	{
		$this->extOptions = $extOptions;
	}
	
	/**
	 * @param WebexXmlEventEmailTemplatesType $emailTemplates
	 */
	public function setEmailTemplates(WebexXmlEventEmailTemplatesType $emailTemplates)
	{
		$this->emailTemplates = $emailTemplates;
	}
	
	/**
	 * @param WebexXmlServMeetingAssistType $assistService
	 */
	public function setAssistService(WebexXmlServMeetingAssistType $assistService)
	{
		$this->assistService = $assistService;
	}
	
}
		
