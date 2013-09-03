<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteQueueType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 *
	 * @var int
	 */
	protected $numUsers;
	
	/**
	 *
	 * @var string
	 */
	protected $modDate;
	
	/**
	 *
	 * @var WebexXmlServWebACDRoleType
	 */
	protected $type;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'string';
	
			case 'numUsers':
				return 'int';
	
			case 'modDate':
				return 'string';
	
			case 'type':
				return 'WebexXmlServWebACDRoleType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'name',
			'numUsers',
			'modDate',
			'type',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'name',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'queueType';
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * @return string $name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param int $numUsers
	 */
	public function setNumUsers($numUsers)
	{
		$this->numUsers = $numUsers;
	}
	
	/**
	 * @return int $numUsers
	 */
	public function getNumUsers()
	{
		return $this->numUsers;
	}
	
	/**
	 * @param string $modDate
	 */
	public function setModDate($modDate)
	{
		$this->modDate = $modDate;
	}
	
	/**
	 * @return string $modDate
	 */
	public function getModDate()
	{
		return $this->modDate;
	}
	
	/**
	 * @param WebexXmlServWebACDRoleType $type
	 */
	public function setType(WebexXmlServWebACDRoleType $type)
	{
		$this->type = $type;
	}
	
	/**
	 * @return WebexXmlServWebACDRoleType $type
	 */
	public function getType()
	{
		return $this->type;
	}
	
}
		
