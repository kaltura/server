<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSalesCenterInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlUseSalesRoleType
	 */
	protected $roles;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $manager;
	
	/**
	 *
	 * @var WebexXmlUseSalesSmeInstanceType
	 */
	protected $smeInfo;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'roles':
				return 'WebexXmlUseSalesRoleType';
	
			case 'manager':
				return 'WebexXml';
	
			case 'smeInfo':
				return 'WebexXmlUseSalesSmeInstanceType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'roles',
			'manager',
			'smeInfo',
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
		return 'salesCenterInstanceType';
	}
	
	/**
	 * @param WebexXmlUseSalesRoleType $roles
	 */
	public function setRoles(WebexXmlUseSalesRoleType $roles)
	{
		$this->roles = $roles;
	}
	
	/**
	 * @return WebexXmlUseSalesRoleType $roles
	 */
	public function getRoles()
	{
		return $this->roles;
	}
	
	/**
	 * @param WebexXml $manager
	 */
	public function setManager(WebexXml $manager)
	{
		$this->manager = $manager;
	}
	
	/**
	 * @return WebexXml $manager
	 */
	public function getManager()
	{
		return $this->manager;
	}
	
	/**
	 * @param WebexXmlUseSalesSmeInstanceType $smeInfo
	 */
	public function setSmeInfo(WebexXmlUseSalesSmeInstanceType $smeInfo)
	{
		$this->smeInfo = $smeInfo;
	}
	
	/**
	 * @return WebexXmlUseSalesSmeInstanceType $smeInfo
	 */
	public function getSmeInfo()
	{
		return $this->smeInfo;
	}
	
}
		
