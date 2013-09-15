<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionLabInfoType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $labID;
	
	/**
	 *
	 * @var string
	 */
	protected $labName;
	
	/**
	 *
	 * @var string
	 */
	protected $description;
	
	/**
	 *
	 * @var integer
	 */
	protected $totalComputers;
	
	/**
	 *
	 * @var integer
	 */
	protected $computersInSession;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'labID':
				return 'long';
	
			case 'labName':
				return 'string';
	
			case 'description':
				return 'string';
	
			case 'totalComputers':
				return 'integer';
	
			case 'computersInSession':
				return 'integer';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'labID',
			'labName',
			'description',
			'totalComputers',
			'computersInSession',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'labID',
			'labName',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'labInfoType';
	}
	
	/**
	 * @param long $labID
	 */
	public function setLabID($labID)
	{
		$this->labID = $labID;
	}
	
	/**
	 * @return long $labID
	 */
	public function getLabID()
	{
		return $this->labID;
	}
	
	/**
	 * @param string $labName
	 */
	public function setLabName($labName)
	{
		$this->labName = $labName;
	}
	
	/**
	 * @return string $labName
	 */
	public function getLabName()
	{
		return $this->labName;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return string $description
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * @param integer $totalComputers
	 */
	public function setTotalComputers($totalComputers)
	{
		$this->totalComputers = $totalComputers;
	}
	
	/**
	 * @return integer $totalComputers
	 */
	public function getTotalComputers()
	{
		return $this->totalComputers;
	}
	
	/**
	 * @param integer $computersInSession
	 */
	public function setComputersInSession($computersInSession)
	{
		$this->computersInSession = $computersInSession;
	}
	
	/**
	 * @return integer $computersInSession
	 */
	public function getComputersInSession()
	{
		return $this->computersInSession;
	}
	
}
		
