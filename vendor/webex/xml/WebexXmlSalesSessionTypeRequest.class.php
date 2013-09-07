<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSalesSessionType.class.php');
require_once(__DIR__ . '/WebexXmlSalesAccessControlType.class.php');
require_once(__DIR__ . '/WebexXmlSalesMetaDataType.class.php');
require_once(__DIR__ . '/WebexXmlSalesScheduleType.class.php');
require_once(__DIR__ . '/WebexXmlSalesEnableOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlSalesTelephonyType.class.php');
require_once(__DIR__ . '/WebexXmlComTrackingType.class.php');
require_once(__DIR__ . '/WebexXmlSalesRepeatType.class.php');
require_once(__DIR__ . '/WebexXmlSalesRemindType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlSalesProspectType.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXmlSalesAttendeeOptionsType.class.php');

class WebexXmlSalesSessionTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlSalesAccessControlType
	 */
	protected $accessControl;
	
	/**
	 *
	 * @var WebexXmlSalesMetaDataType
	 */
	protected $metaData;
	
	/**
	 *
	 * @var WebexXmlSalesScheduleType
	 */
	protected $schedule;
	
	/**
	 *
	 * @var WebexXmlSalesEnableOptionsType
	 */
	protected $enableOptions;
	
	/**
	 *
	 * @var WebexXmlSalesTelephonyType
	 */
	protected $telephony;
	
	/**
	 *
	 * @var WebexXmlComTrackingType
	 */
	protected $tracking;
	
	/**
	 *
	 * @var WebexXmlSalesRepeatType
	 */
	protected $repeat;
	
	/**
	 *
	 * @var WebexXmlSalesRemindType
	 */
	protected $remind;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSalesProspectType>
	 */
	protected $prospects;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $salesTeam;
	
	/**
	 *
	 * @var WebexXmlSalesAttendeeOptionsType
	 */
	protected $attendeeOptions;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'accessControl',
			'metaData',
			'schedule',
			'enableOptions',
			'telephony',
			'tracking',
			'repeat',
			'remind',
			'prospects',
			'salesTeam',
			'attendeeOptions',
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
		return 'sales';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'sales:salesSessionType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSalesSessionType';
	}
	
	/**
	 * @param WebexXmlSalesAccessControlType $accessControl
	 */
	public function setAccessControl(WebexXmlSalesAccessControlType $accessControl)
	{
		$this->accessControl = $accessControl;
	}
	
	/**
	 * @param WebexXmlSalesMetaDataType $metaData
	 */
	public function setMetaData(WebexXmlSalesMetaDataType $metaData)
	{
		$this->metaData = $metaData;
	}
	
	/**
	 * @param WebexXmlSalesScheduleType $schedule
	 */
	public function setSchedule(WebexXmlSalesScheduleType $schedule)
	{
		$this->schedule = $schedule;
	}
	
	/**
	 * @param WebexXmlSalesEnableOptionsType $enableOptions
	 */
	public function setEnableOptions(WebexXmlSalesEnableOptionsType $enableOptions)
	{
		$this->enableOptions = $enableOptions;
	}
	
	/**
	 * @param WebexXmlSalesTelephonyType $telephony
	 */
	public function setTelephony(WebexXmlSalesTelephonyType $telephony)
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
	 * @param WebexXmlSalesRepeatType $repeat
	 */
	public function setRepeat(WebexXmlSalesRepeatType $repeat)
	{
		$this->repeat = $repeat;
	}
	
	/**
	 * @param WebexXmlSalesRemindType $remind
	 */
	public function setRemind(WebexXmlSalesRemindType $remind)
	{
		$this->remind = $remind;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlSalesProspectType> $prospects
	 */
	public function setProspects(WebexXmlArray $prospects)
	{
		if($prospects->getType() != 'WebexXmlSalesProspectType')
			throw new WebexXmlException(get_class($this) . "::prospects must be of type WebexXmlSalesProspectType");
		
		$this->prospects = $prospects;
	}
	
	/**
	 * @param WebexXml $salesTeam
	 */
	public function setSalesTeam(WebexXml $salesTeam)
	{
		$this->salesTeam = $salesTeam;
	}
	
	/**
	 * @param WebexXmlSalesAttendeeOptionsType $attendeeOptions
	 */
	public function setAttendeeOptions(WebexXmlSalesAttendeeOptionsType $attendeeOptions)
	{
		$this->attendeeOptions = $attendeeOptions;
	}
	
}

