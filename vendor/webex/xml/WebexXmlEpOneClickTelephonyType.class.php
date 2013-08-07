<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpOneClickTelephonyType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlMeetTelephonySupportType
	 */
	protected $telephonySupport;
	
	/**
	 *
	 * @var string
	 */
	protected $extTelephonyDescription;
	
	/**
	 *
	 * @var int
	 */
	protected $tspAccountIndex;
	
	/**
	 *
	 * @var int
	 */
	protected $personalAccountIndex;
	
	/**
	 *
	 * @var WebexXmlEpOneClickAccountType
	 */
	protected $account;
	
	/**
	 *
	 * @var WebexXmlEpOneClickAccountLabelType
	 */
	protected $accountLabel;
	
	/**
	 *
	 * @var string
	 */
	protected $teleconfServiceName;
	
	/**
	 *
	 * @var string
	 */
	protected $teleconfLocation;
	
	/**
	 *
	 * @var boolean
	 */
	protected $intlLocalCallIn;
	
	/**
	 *
	 * @var boolean
	 */
	protected $tollfree;
	
	/**
	 *
	 * @var WebexXmlComEntryExitToneType
	 */
	protected $entryExitTone;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isMPAudio;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'telephonySupport':
				return 'WebexXmlMeetTelephonySupportType';
	
			case 'extTelephonyDescription':
				return 'string';
	
			case 'tspAccountIndex':
				return 'int';
	
			case 'personalAccountIndex':
				return 'int';
	
			case 'account':
				return 'WebexXmlEpOneClickAccountType';
	
			case 'accountLabel':
				return 'WebexXmlEpOneClickAccountLabelType';
	
			case 'teleconfServiceName':
				return 'string';
	
			case 'teleconfLocation':
				return 'string';
	
			case 'intlLocalCallIn':
				return 'boolean';
	
			case 'tollfree':
				return 'boolean';
	
			case 'entryExitTone':
				return 'WebexXmlComEntryExitToneType';
	
			case 'isMPAudio':
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
			'extTelephonyDescription',
			'tspAccountIndex',
			'personalAccountIndex',
			'account',
			'accountLabel',
			'teleconfServiceName',
			'teleconfLocation',
			'intlLocalCallIn',
			'tollfree',
			'entryExitTone',
			'isMPAudio',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'telephonySupport',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'oneClickTelephonyType';
	}
	
	/**
	 * @param WebexXmlMeetTelephonySupportType $telephonySupport
	 */
	public function setTelephonySupport(WebexXmlMeetTelephonySupportType $telephonySupport)
	{
		$this->telephonySupport = $telephonySupport;
	}
	
	/**
	 * @return WebexXmlMeetTelephonySupportType $telephonySupport
	 */
	public function getTelephonySupport()
	{
		return $this->telephonySupport;
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
	 * @param int $tspAccountIndex
	 */
	public function setTspAccountIndex($tspAccountIndex)
	{
		$this->tspAccountIndex = $tspAccountIndex;
	}
	
	/**
	 * @return int $tspAccountIndex
	 */
	public function getTspAccountIndex()
	{
		return $this->tspAccountIndex;
	}
	
	/**
	 * @param int $personalAccountIndex
	 */
	public function setPersonalAccountIndex($personalAccountIndex)
	{
		$this->personalAccountIndex = $personalAccountIndex;
	}
	
	/**
	 * @return int $personalAccountIndex
	 */
	public function getPersonalAccountIndex()
	{
		return $this->personalAccountIndex;
	}
	
	/**
	 * @param WebexXmlEpOneClickAccountType $account
	 */
	public function setAccount(WebexXmlEpOneClickAccountType $account)
	{
		$this->account = $account;
	}
	
	/**
	 * @return WebexXmlEpOneClickAccountType $account
	 */
	public function getAccount()
	{
		return $this->account;
	}
	
	/**
	 * @param WebexXmlEpOneClickAccountLabelType $accountLabel
	 */
	public function setAccountLabel(WebexXmlEpOneClickAccountLabelType $accountLabel)
	{
		$this->accountLabel = $accountLabel;
	}
	
	/**
	 * @return WebexXmlEpOneClickAccountLabelType $accountLabel
	 */
	public function getAccountLabel()
	{
		return $this->accountLabel;
	}
	
	/**
	 * @param string $teleconfServiceName
	 */
	public function setTeleconfServiceName($teleconfServiceName)
	{
		$this->teleconfServiceName = $teleconfServiceName;
	}
	
	/**
	 * @return string $teleconfServiceName
	 */
	public function getTeleconfServiceName()
	{
		return $this->teleconfServiceName;
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
	 * @param boolean $tollfree
	 */
	public function setTollfree($tollfree)
	{
		$this->tollfree = $tollfree;
	}
	
	/**
	 * @return boolean $tollfree
	 */
	public function getTollfree()
	{
		return $this->tollfree;
	}
	
	/**
	 * @param WebexXmlComEntryExitToneType $entryExitTone
	 */
	public function setEntryExitTone(WebexXmlComEntryExitToneType $entryExitTone)
	{
		$this->entryExitTone = $entryExitTone;
	}
	
	/**
	 * @return WebexXmlComEntryExitToneType $entryExitTone
	 */
	public function getEntryExitTone()
	{
		return $this->entryExitTone;
	}
	
	/**
	 * @param boolean $isMPAudio
	 */
	public function setIsMPAudio($isMPAudio)
	{
		$this->isMPAudio = $isMPAudio;
	}
	
	/**
	 * @return boolean $isMPAudio
	 */
	public function getIsMPAudio()
	{
		return $this->isMPAudio;
	}
	
}
		
