<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesTelephonyType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlSalesTelephonySupportType
	 */
	protected $telephonySupport;
	
	/**
	 *
	 * @var integer
	 */
	protected $numPhoneLines;
	
	/**
	 *
	 * @var string
	 */
	protected $extTelephonyURL;
	
	/**
	 *
	 * @var string
	 */
	protected $extTelephonyDescription;
	
	/**
	 *
	 * @var boolean
	 */
	protected $enableTSP;
	
	/**
	 *
	 * @var integer
	 */
	protected $tspAccountIndex;
	
	/**
	 *
	 * @var integer
	 */
	protected $personalAccountIndex;
	
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
	 * @var WebexXmlServCallInNumType
	 */
	protected $callInNum;
	
	/**
	 *
	 * @var WebexXmlServTspAccountType
	 */
	protected $tspConference;
	
	/**
	 *
	 * @var WebexXmlServTspAccessCodeOrderType
	 */
	protected $tspAccessCodeOrder;
	
	/**
	 *
	 * @var boolean
	 */
	protected $tollFree;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'telephonySupport':
				return 'WebexXmlSalesTelephonySupportType';
	
			case 'numPhoneLines':
				return 'integer';
	
			case 'extTelephonyURL':
				return 'string';
	
			case 'extTelephonyDescription':
				return 'string';
	
			case 'enableTSP':
				return 'boolean';
	
			case 'tspAccountIndex':
				return 'integer';
	
			case 'personalAccountIndex':
				return 'integer';
	
			case 'intlLocalCallIn':
				return 'boolean';
	
			case 'teleconfLocation':
				return 'string';
	
			case 'callInNum':
				return 'WebexXmlServCallInNumType';
	
			case 'tspConference':
				return 'WebexXmlServTspAccountType';
	
			case 'tspAccessCodeOrder':
				return 'WebexXmlServTspAccessCodeOrderType';
	
			case 'tollFree':
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
			'telephonySupport',
			'numPhoneLines',
			'extTelephonyURL',
			'extTelephonyDescription',
			'enableTSP',
			'tspAccountIndex',
			'personalAccountIndex',
			'intlLocalCallIn',
			'teleconfLocation',
			'callInNum',
			'tspConference',
			'tspAccessCodeOrder',
			'tollFree',
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
		return 'telephonyType';
	}
	
	/**
	 * @param WebexXmlSalesTelephonySupportType $telephonySupport
	 */
	public function setTelephonySupport(WebexXmlSalesTelephonySupportType $telephonySupport)
	{
		$this->telephonySupport = $telephonySupport;
	}
	
	/**
	 * @return WebexXmlSalesTelephonySupportType $telephonySupport
	 */
	public function getTelephonySupport()
	{
		return $this->telephonySupport;
	}
	
	/**
	 * @param integer $numPhoneLines
	 */
	public function setNumPhoneLines($numPhoneLines)
	{
		$this->numPhoneLines = $numPhoneLines;
	}
	
	/**
	 * @return integer $numPhoneLines
	 */
	public function getNumPhoneLines()
	{
		return $this->numPhoneLines;
	}
	
	/**
	 * @param string $extTelephonyURL
	 */
	public function setExtTelephonyURL($extTelephonyURL)
	{
		$this->extTelephonyURL = $extTelephonyURL;
	}
	
	/**
	 * @return string $extTelephonyURL
	 */
	public function getExtTelephonyURL()
	{
		return $this->extTelephonyURL;
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
	 * @param boolean $enableTSP
	 */
	public function setEnableTSP($enableTSP)
	{
		$this->enableTSP = $enableTSP;
	}
	
	/**
	 * @return boolean $enableTSP
	 */
	public function getEnableTSP()
	{
		return $this->enableTSP;
	}
	
	/**
	 * @param integer $tspAccountIndex
	 */
	public function setTspAccountIndex($tspAccountIndex)
	{
		$this->tspAccountIndex = $tspAccountIndex;
	}
	
	/**
	 * @return integer $tspAccountIndex
	 */
	public function getTspAccountIndex()
	{
		return $this->tspAccountIndex;
	}
	
	/**
	 * @param integer $personalAccountIndex
	 */
	public function setPersonalAccountIndex($personalAccountIndex)
	{
		$this->personalAccountIndex = $personalAccountIndex;
	}
	
	/**
	 * @return integer $personalAccountIndex
	 */
	public function getPersonalAccountIndex()
	{
		return $this->personalAccountIndex;
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
	 * @param WebexXmlServCallInNumType $callInNum
	 */
	public function setCallInNum(WebexXmlServCallInNumType $callInNum)
	{
		$this->callInNum = $callInNum;
	}
	
	/**
	 * @return WebexXmlServCallInNumType $callInNum
	 */
	public function getCallInNum()
	{
		return $this->callInNum;
	}
	
	/**
	 * @param WebexXmlServTspAccountType $tspConference
	 */
	public function setTspConference(WebexXmlServTspAccountType $tspConference)
	{
		$this->tspConference = $tspConference;
	}
	
	/**
	 * @return WebexXmlServTspAccountType $tspConference
	 */
	public function getTspConference()
	{
		return $this->tspConference;
	}
	
	/**
	 * @param WebexXmlServTspAccessCodeOrderType $tspAccessCodeOrder
	 */
	public function setTspAccessCodeOrder(WebexXmlServTspAccessCodeOrderType $tspAccessCodeOrder)
	{
		$this->tspAccessCodeOrder = $tspAccessCodeOrder;
	}
	
	/**
	 * @return WebexXmlServTspAccessCodeOrderType $tspAccessCodeOrder
	 */
	public function getTspAccessCodeOrder()
	{
		return $this->tspAccessCodeOrder;
	}
	
	/**
	 * @param boolean $tollFree
	 */
	public function setTollFree($tollFree)
	{
		$this->tollFree = $tollFree;
	}
	
	/**
	 * @return boolean $tollFree
	 */
	public function getTollFree()
	{
		return $this->tollFree;
	}
	
}
		
