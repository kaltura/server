<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionTrainingSessionType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlTrainTrainingMetaDataType
	 */
	protected $metaData;
	
	/**
	 *
	 * @var WebexXmlTrainTrainingEnableOptionsType
	 */
	protected $enableOptions;
	
	/**
	 *
	 * @var WebexXmlSessTelephonyType
	 */
	protected $telephony;
	
	/**
	 *
	 * @var WebexXmlComTrackingType
	 */
	protected $tracking;
	
	/**
	 *
	 * @var WebexXmlTrainTrainRepeatType
	 */
	protected $repeat;
	
	/**
	 *
	 * @var WebexXmlSessRemindType
	 */
	protected $remind;
	
	/**
	 *
	 * @var WebexXmlSessParticipantsType
	 */
	protected $presenters;
	
	/**
	 *
	 * @var WebexXmlSessParticipantsType
	 */
	protected $attendees;
	
	/**
	 *
	 * @var WebexXmlTrainAttendeeOptionsType
	 */
	protected $attendeeOptions;
	
	/**
	 *
	 * @var WebexXmlTrainHandsOnLabType
	 */
	protected $handsOnLab;
	
	/**
	 *
	 * @var WebexXmlComPsoFieldsType
	 */
	protected $psoFields;
	
	/**
	 *
	 * @var WebexXmlServMeetingAssistType
	 */
	protected $assistService;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $preAssignBreakout;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'metaData':
				return 'WebexXmlTrainTrainingMetaDataType';
	
			case 'enableOptions':
				return 'WebexXmlTrainTrainingEnableOptionsType';
	
			case 'telephony':
				return 'WebexXmlSessTelephonyType';
	
			case 'tracking':
				return 'WebexXmlComTrackingType';
	
			case 'repeat':
				return 'WebexXmlTrainTrainRepeatType';
	
			case 'remind':
				return 'WebexXmlSessRemindType';
	
			case 'presenters':
				return 'WebexXmlSessParticipantsType';
	
			case 'attendees':
				return 'WebexXmlSessParticipantsType';
	
			case 'attendeeOptions':
				return 'WebexXmlTrainAttendeeOptionsType';
	
			case 'handsOnLab':
				return 'WebexXmlTrainHandsOnLabType';
	
			case 'psoFields':
				return 'WebexXmlComPsoFieldsType';
	
			case 'assistService':
				return 'WebexXmlServMeetingAssistType';
	
			case 'preAssignBreakout':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'metaData',
			'enableOptions',
			'telephony',
			'tracking',
			'repeat',
			'remind',
			'presenters',
			'attendees',
			'attendeeOptions',
			'handsOnLab',
			'psoFields',
			'assistService',
			'preAssignBreakout',
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
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'trainingSessionType';
	}
	
	/**
	 * @param WebexXmlTrainTrainingMetaDataType $metaData
	 */
	public function setMetaData(WebexXmlTrainTrainingMetaDataType $metaData)
	{
		$this->metaData = $metaData;
	}
	
	/**
	 * @return WebexXmlTrainTrainingMetaDataType $metaData
	 */
	public function getMetaData()
	{
		return $this->metaData;
	}
	
	/**
	 * @param WebexXmlTrainTrainingEnableOptionsType $enableOptions
	 */
	public function setEnableOptions(WebexXmlTrainTrainingEnableOptionsType $enableOptions)
	{
		$this->enableOptions = $enableOptions;
	}
	
	/**
	 * @return WebexXmlTrainTrainingEnableOptionsType $enableOptions
	 */
	public function getEnableOptions()
	{
		return $this->enableOptions;
	}
	
	/**
	 * @param WebexXmlSessTelephonyType $telephony
	 */
	public function setTelephony(WebexXmlSessTelephonyType $telephony)
	{
		$this->telephony = $telephony;
	}
	
	/**
	 * @return WebexXmlSessTelephonyType $telephony
	 */
	public function getTelephony()
	{
		return $this->telephony;
	}
	
	/**
	 * @param WebexXmlComTrackingType $tracking
	 */
	public function setTracking(WebexXmlComTrackingType $tracking)
	{
		$this->tracking = $tracking;
	}
	
	/**
	 * @return WebexXmlComTrackingType $tracking
	 */
	public function getTracking()
	{
		return $this->tracking;
	}
	
	/**
	 * @param WebexXmlTrainTrainRepeatType $repeat
	 */
	public function setRepeat(WebexXmlTrainTrainRepeatType $repeat)
	{
		$this->repeat = $repeat;
	}
	
	/**
	 * @return WebexXmlTrainTrainRepeatType $repeat
	 */
	public function getRepeat()
	{
		return $this->repeat;
	}
	
	/**
	 * @param WebexXmlSessRemindType $remind
	 */
	public function setRemind(WebexXmlSessRemindType $remind)
	{
		$this->remind = $remind;
	}
	
	/**
	 * @return WebexXmlSessRemindType $remind
	 */
	public function getRemind()
	{
		return $this->remind;
	}
	
	/**
	 * @param WebexXmlSessParticipantsType $presenters
	 */
	public function setPresenters(WebexXmlSessParticipantsType $presenters)
	{
		$this->presenters = $presenters;
	}
	
	/**
	 * @return WebexXmlSessParticipantsType $presenters
	 */
	public function getPresenters()
	{
		return $this->presenters;
	}
	
	/**
	 * @param WebexXmlSessParticipantsType $attendees
	 */
	public function setAttendees(WebexXmlSessParticipantsType $attendees)
	{
		$this->attendees = $attendees;
	}
	
	/**
	 * @return WebexXmlSessParticipantsType $attendees
	 */
	public function getAttendees()
	{
		return $this->attendees;
	}
	
	/**
	 * @param WebexXmlTrainAttendeeOptionsType $attendeeOptions
	 */
	public function setAttendeeOptions(WebexXmlTrainAttendeeOptionsType $attendeeOptions)
	{
		$this->attendeeOptions = $attendeeOptions;
	}
	
	/**
	 * @return WebexXmlTrainAttendeeOptionsType $attendeeOptions
	 */
	public function getAttendeeOptions()
	{
		return $this->attendeeOptions;
	}
	
	/**
	 * @param WebexXmlTrainHandsOnLabType $handsOnLab
	 */
	public function setHandsOnLab(WebexXmlTrainHandsOnLabType $handsOnLab)
	{
		$this->handsOnLab = $handsOnLab;
	}
	
	/**
	 * @return WebexXmlTrainHandsOnLabType $handsOnLab
	 */
	public function getHandsOnLab()
	{
		return $this->handsOnLab;
	}
	
	/**
	 * @param WebexXmlComPsoFieldsType $psoFields
	 */
	public function setPsoFields(WebexXmlComPsoFieldsType $psoFields)
	{
		$this->psoFields = $psoFields;
	}
	
	/**
	 * @return WebexXmlComPsoFieldsType $psoFields
	 */
	public function getPsoFields()
	{
		return $this->psoFields;
	}
	
	/**
	 * @param WebexXmlServMeetingAssistType $assistService
	 */
	public function setAssistService(WebexXmlServMeetingAssistType $assistService)
	{
		$this->assistService = $assistService;
	}
	
	/**
	 * @return WebexXmlServMeetingAssistType $assistService
	 */
	public function getAssistService()
	{
		return $this->assistService;
	}
	
	/**
	 * @param WebexXml $preAssignBreakout
	 */
	public function setPreAssignBreakout(WebexXml $preAssignBreakout)
	{
		$this->preAssignBreakout = $preAssignBreakout;
	}
	
	/**
	 * @return WebexXml $preAssignBreakout
	 */
	public function getPreAssignBreakout()
	{
		return $this->preAssignBreakout;
	}
	
}
		
