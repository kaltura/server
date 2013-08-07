<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpOcTelephonyType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlEpTelephonySupportType
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
				return 'WebexXmlEpTelephonySupportType';
	
			case 'extTelephonyDescription':
				return 'string';
	
			case 'tspAccountIndex':
				return 'int';
	
			case 'personalAccountIndex':
				return 'int';
	
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
		return 'ocTelephonyType';
	}
	
	/**
	 * @param WebexXmlEpTelephonySupportType $telephonySupport
	 */
	public function setTelephonySupport(WebexXmlEpTelephonySupportType $telephonySupport)
	{
		$this->telephonySupport = $telephonySupport;
	}
	
	/**
	 * @return WebexXmlEpTelephonySupportType $telephonySupport
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
		
