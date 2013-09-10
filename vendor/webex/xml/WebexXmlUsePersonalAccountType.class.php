<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUsePersonalAccountType extends WebexXmlRequestType
{
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
	protected $accountIndex;
	
	/**
	 *
	 * @var boolean
	 */
	protected $defaultFlag;
	
	/**
	 *
	 * @var boolean
	 */
	protected $autoGenerate;
	
	/**
	 *
	 * @var boolean
	 */
	protected $joinBeforeHost;
	
	/**
	 *
	 * @var boolean
	 */
	protected $delete;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'subscriberAccessCode':
				return 'string';
	
			case 'participantFullAccessCode':
				return 'string';
	
			case 'participantLimitedAccessCode':
				return 'string';
	
			case 'accountIndex':
				return 'integer';
	
			case 'defaultFlag':
				return 'boolean';
	
			case 'autoGenerate':
				return 'boolean';
	
			case 'joinBeforeHost':
				return 'boolean';
	
			case 'delete':
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
			'subscriberAccessCode',
			'participantFullAccessCode',
			'participantLimitedAccessCode',
			'accountIndex',
			'defaultFlag',
			'autoGenerate',
			'joinBeforeHost',
			'delete',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'accountIndex',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'personalAccountType';
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
	 * @param boolean $defaultFlag
	 */
	public function setDefaultFlag($defaultFlag)
	{
		$this->defaultFlag = $defaultFlag;
	}
	
	/**
	 * @return boolean $defaultFlag
	 */
	public function getDefaultFlag()
	{
		return $this->defaultFlag;
	}
	
	/**
	 * @param boolean $autoGenerate
	 */
	public function setAutoGenerate($autoGenerate)
	{
		$this->autoGenerate = $autoGenerate;
	}
	
	/**
	 * @return boolean $autoGenerate
	 */
	public function getAutoGenerate()
	{
		return $this->autoGenerate;
	}
	
	/**
	 * @param boolean $joinBeforeHost
	 */
	public function setJoinBeforeHost($joinBeforeHost)
	{
		$this->joinBeforeHost = $joinBeforeHost;
	}
	
	/**
	 * @return boolean $joinBeforeHost
	 */
	public function getJoinBeforeHost()
	{
		return $this->joinBeforeHost;
	}
	
	/**
	 * @param boolean $delete
	 */
	public function setDelete($delete)
	{
		$this->delete = $delete;
	}
	
	/**
	 * @return boolean $delete
	 */
	public function getDelete()
	{
		return $this->delete;
	}
	
}
		
