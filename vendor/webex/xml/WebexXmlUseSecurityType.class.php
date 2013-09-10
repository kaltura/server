<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSecurityType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $forceChangePassword;
	
	/**
	 *
	 * @var boolean
	 */
	protected $resetPassword;
	
	/**
	 *
	 * @var boolean
	 */
	protected $lockAccount;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'forceChangePassword':
				return 'boolean';
	
			case 'resetPassword':
				return 'boolean';
	
			case 'lockAccount':
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
			'forceChangePassword',
			'resetPassword',
			'lockAccount',
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
		return 'securityType';
	}
	
	/**
	 * @param boolean $forceChangePassword
	 */
	public function setForceChangePassword($forceChangePassword)
	{
		$this->forceChangePassword = $forceChangePassword;
	}
	
	/**
	 * @return boolean $forceChangePassword
	 */
	public function getForceChangePassword()
	{
		return $this->forceChangePassword;
	}
	
	/**
	 * @param boolean $resetPassword
	 */
	public function setResetPassword($resetPassword)
	{
		$this->resetPassword = $resetPassword;
	}
	
	/**
	 * @return boolean $resetPassword
	 */
	public function getResetPassword()
	{
		return $this->resetPassword;
	}
	
	/**
	 * @param boolean $lockAccount
	 */
	public function setLockAccount($lockAccount)
	{
		$this->lockAccount = $lockAccount;
	}
	
	/**
	 * @return boolean $lockAccount
	 */
	public function getLockAccount()
	{
		return $this->lockAccount;
	}
	
}

