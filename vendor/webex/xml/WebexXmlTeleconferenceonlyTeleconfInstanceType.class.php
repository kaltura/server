<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTeleconferenceonlyTeleconfInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $personalConferenceNumber;
	
	/**
	 *
	 * @var integer
	 */
	protected $accountIndex;
	
	/**
	 *
	 * @var string
	 */
	protected $tollFreeCallInNumber;
	
	/**
	 *
	 * @var string
	 */
	protected $tollCallInNumber;
	
	/**
	 *
	 * @var string
	 */
	protected $intlLocalCallInNumber;
	
	/**
	 *
	 * @var string
	 */
	protected $subscriberAccessCode;
	
	/**
	 *
	 * @var string
	 */
	protected $participantFullAccessCode;
	
	/**
	 *
	 * @var string
	 */
	protected $participantLimitedAccessCode;
	
	/**
	 *
	 * @var integer
	 */
	protected $scheduleConfID;
	
	/**
	 *
	 * @var string
	 */
	protected $extTelephonyDescription;
	
	/**
	 *
	 * @var boolean
	 */
	protected $intlLocalCallIn;
	
	/**
	 *
	 * @var string
	 */
	protected $teleconfLocation;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlServGlobalCallInNumType>
	 */
	protected $globalNum;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'personalConferenceNumber':
				return 'boolean';
	
			case 'accountIndex':
				return 'integer';
	
			case 'tollFreeCallInNumber':
				return 'string';
	
			case 'tollCallInNumber':
				return 'string';
	
			case 'intlLocalCallInNumber':
				return 'string';
	
			case 'subscriberAccessCode':
				return 'string';
	
			case 'participantFullAccessCode':
				return 'string';
	
			case 'participantLimitedAccessCode':
				return 'string';
	
			case 'scheduleConfID':
				return 'integer';
	
			case 'extTelephonyDescription':
				return 'string';
	
			case 'intlLocalCallIn':
				return 'boolean';
	
			case 'teleconfLocation':
				return 'string';
	
			case 'globalNum':
				return 'WebexXmlArray<WebexXmlServGlobalCallInNumType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'personalConferenceNumber',
			'accountIndex',
			'tollFreeCallInNumber',
			'tollCallInNumber',
			'intlLocalCallInNumber',
			'subscriberAccessCode',
			'participantFullAccessCode',
			'participantLimitedAccessCode',
			'scheduleConfID',
			'extTelephonyDescription',
			'intlLocalCallIn',
			'teleconfLocation',
			'globalNum',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'personalConferenceNumber',
			'tollFreeCallInNumber',
			'tollCallInNumber',
			'intlLocalCallInNumber',
			'subscriberAccessCode',
			'participantFullAccessCode',
			'participantLimitedAccessCode',
			'scheduleConfID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'teleconfInstanceType';
	}
	
	/**
	 * @param boolean $personalConferenceNumber
	 */
	public function setPersonalConferenceNumber($personalConferenceNumber)
	{
		$this->personalConferenceNumber = $personalConferenceNumber;
	}
	
	/**
	 * @return boolean $personalConferenceNumber
	 */
	public function getPersonalConferenceNumber()
	{
		return $this->personalConferenceNumber;
	}
	
	/**
	 * @param integer $accountIndex
	 */
	public function setAccountIndex($accountIndex)
	{
		$this->accountIndex = $accountIndex;
	}
	
	/**
	 * @return integer $accountIndex
	 */
	public function getAccountIndex()
	{
		return $this->accountIndex;
	}
	
	/**
	 * @param string $tollFreeCallInNumber
	 */
	public function setTollFreeCallInNumber($tollFreeCallInNumber)
	{
		$this->tollFreeCallInNumber = $tollFreeCallInNumber;
	}
	
	/**
	 * @return string $tollFreeCallInNumber
	 */
	public function getTollFreeCallInNumber()
	{
		return $this->tollFreeCallInNumber;
	}
	
	/**
	 * @param string $tollCallInNumber
	 */
	public function setTollCallInNumber($tollCallInNumber)
	{
		$this->tollCallInNumber = $tollCallInNumber;
	}
	
	/**
	 * @return string $tollCallInNumber
	 */
	public function getTollCallInNumber()
	{
		return $this->tollCallInNumber;
	}
	
	/**
	 * @param string $intlLocalCallInNumber
	 */
	public function setIntlLocalCallInNumber($intlLocalCallInNumber)
	{
		$this->intlLocalCallInNumber = $intlLocalCallInNumber;
	}
	
	/**
	 * @return string $intlLocalCallInNumber
	 */
	public function getIntlLocalCallInNumber()
	{
		return $this->intlLocalCallInNumber;
	}
	
	/**
	 * @param string $subscriberAccessCode
	 */
	public function setSubscriberAccessCode($subscriberAccessCode)
	{
		$this->subscriberAccessCode = $subscriberAccessCode;
	}
	
	/**
	 * @return string $subscriberAccessCode
	 */
	public function getSubscriberAccessCode()
	{
		return $this->subscriberAccessCode;
	}
	
	/**
	 * @param string $participantFullAccessCode
	 */
	public function setParticipantFullAccessCode($participantFullAccessCode)
	{
		$this->participantFullAccessCode = $participantFullAccessCode;
	}
	
	/**
	 * @return string $participantFullAccessCode
	 */
	public function getParticipantFullAccessCode()
	{
		return $this->participantFullAccessCode;
	}
	
	/**
	 * @param string $participantLimitedAccessCode
	 */
	public function setParticipantLimitedAccessCode($participantLimitedAccessCode)
	{
		$this->participantLimitedAccessCode = $participantLimitedAccessCode;
	}
	
	/**
	 * @return string $participantLimitedAccessCode
	 */
	public function getParticipantLimitedAccessCode()
	{
		return $this->participantLimitedAccessCode;
	}
	
	/**
	 * @param integer $scheduleConfID
	 */
	public function setScheduleConfID($scheduleConfID)
	{
		$this->scheduleConfID = $scheduleConfID;
	}
	
	/**
	 * @return integer $scheduleConfID
	 */
	public function getScheduleConfID()
	{
		return $this->scheduleConfID;
	}
	
	/**
	 * @param string $extTelephonyDescription
	 */
	public function setExtTelephonyDescription($extTelephonyDescription)
	{
		$this->extTelephonyDescription = $extTelephonyDescription;
	}
	
	/**
	 * @return string $extTelephonyDescription
	 */
	public function getExtTelephonyDescription()
	{
		return $this->extTelephonyDescription;
	}
	
	/**
	 * @param boolean $intlLocalCallIn
	 */
	public function setIntlLocalCallIn($intlLocalCallIn)
	{
		$this->intlLocalCallIn = $intlLocalCallIn;
	}
	
	/**
	 * @return boolean $intlLocalCallIn
	 */
	public function getIntlLocalCallIn()
	{
		return $this->intlLocalCallIn;
	}
	
	/**
	 * @param string $teleconfLocation
	 */
	public function setTeleconfLocation($teleconfLocation)
	{
		$this->teleconfLocation = $teleconfLocation;
	}
	
	/**
	 * @return string $teleconfLocation
	 */
	public function getTeleconfLocation()
	{
		return $this->teleconfLocation;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlServGlobalCallInNumType> $globalNum
	 */
	public function setGlobalNum(WebexXmlArray $globalNum)
	{
		if($globalNum->getType() != 'WebexXmlServGlobalCallInNumType')
			throw new WebexXmlException(get_class($this) . "::globalNum must be of type WebexXmlServGlobalCallInNumType");
		
		$this->globalNum = $globalNum;
	}
	
	/**
	 * @return WebexXmlArray $globalNum
	 */
	public function getGlobalNum()
	{
		return $this->globalNum;
	}
	
}
		
