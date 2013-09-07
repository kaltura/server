<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteDefaultsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $emailReminders;
	
	/**
	 *
	 * @var WebexXmlComEntryExitToneType
	 */
	protected $entryExitTone;
	
	/**
	 *
	 * @var boolean
	 */
	protected $voip;
	
	/**
	 *
	 * @var WebexXmlSiteTeleconferenceType
	 */
	protected $teleconference;
	
	/**
	 *
	 * @var boolean
	 */
	protected $joinTeleconfNotPress1;
	
	/**
	 *
	 * @var boolean
	 */
	protected $updateTSPAccount;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'emailReminders':
				return 'boolean';
	
			case 'entryExitTone':
				return 'WebexXmlComEntryExitToneType';
	
			case 'voip':
				return 'boolean';
	
			case 'teleconference':
				return 'WebexXmlSiteTeleconferenceType';
	
			case 'joinTeleconfNotPress1':
				return 'boolean';
	
			case 'updateTSPAccount':
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
			'emailReminders',
			'entryExitTone',
			'voip',
			'teleconference',
			'joinTeleconfNotPress1',
			'updateTSPAccount',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'emailReminders',
			'entryExitTone',
			'voip',
			'teleconference',
			'joinTeleconfNotPress1',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'defaultsType';
	}
	
	/**
	 * @param boolean $emailReminders
	 */
	public function setEmailReminders($emailReminders)
	{
		$this->emailReminders = $emailReminders;
	}
	
	/**
	 * @return boolean $emailReminders
	 */
	public function getEmailReminders()
	{
		return $this->emailReminders;
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
	 * @param boolean $voip
	 */
	public function setVoip($voip)
	{
		$this->voip = $voip;
	}
	
	/**
	 * @return boolean $voip
	 */
	public function getVoip()
	{
		return $this->voip;
	}
	
	/**
	 * @param WebexXmlSiteTeleconferenceType $teleconference
	 */
	public function setTeleconference(WebexXmlSiteTeleconferenceType $teleconference)
	{
		$this->teleconference = $teleconference;
	}
	
	/**
	 * @return WebexXmlSiteTeleconferenceType $teleconference
	 */
	public function getTeleconference()
	{
		return $this->teleconference;
	}
	
	/**
	 * @param boolean $joinTeleconfNotPress1
	 */
	public function setJoinTeleconfNotPress1($joinTeleconfNotPress1)
	{
		$this->joinTeleconfNotPress1 = $joinTeleconfNotPress1;
	}
	
	/**
	 * @return boolean $joinTeleconfNotPress1
	 */
	public function getJoinTeleconfNotPress1()
	{
		return $this->joinTeleconfNotPress1;
	}
	
	/**
	 * @param boolean $updateTSPAccount
	 */
	public function setUpdateTSPAccount($updateTSPAccount)
	{
		$this->updateTSPAccount = $updateTSPAccount;
	}
	
	/**
	 * @return boolean $updateTSPAccount
	 */
	public function getUpdateTSPAccount()
	{
		return $this->updateTSPAccount;
	}
	
}
		
