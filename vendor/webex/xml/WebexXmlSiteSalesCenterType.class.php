<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteSalesCenterType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $allowJoinWithoutLogin;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'allowJoinWithoutLogin':
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
			'allowJoinWithoutLogin',
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
		return 'SalesCenterType';
	}
	
	/**
	 * @param boolean $allowJoinWithoutLogin
	 */
	public function setAllowJoinWithoutLogin($allowJoinWithoutLogin)
	{
		$this->allowJoinWithoutLogin = $allowJoinWithoutLogin;
	}
	
	/**
	 * @return boolean $allowJoinWithoutLogin
	 */
	public function getAllowJoinWithoutLogin()
	{
		return $this->allowJoinWithoutLogin;
	}
	
}

