<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionHandsOnLabType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $reserveHOL;
	
	/**
	 *
	 * @var string
	 */
	protected $labName;
	
	/**
	 *
	 * @var int
	 */
	protected $numComputers;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'reserveHOL':
				return 'boolean';
	
			case 'labName':
				return 'string';
	
			case 'numComputers':
				return 'int';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'reserveHOL',
			'labName',
			'numComputers',
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
		return 'handsOnLabType';
	}
	
	/**
	 * @param boolean $reserveHOL
	 */
	public function setReserveHOL($reserveHOL)
	{
		$this->reserveHOL = $reserveHOL;
	}
	
	/**
	 * @return boolean $reserveHOL
	 */
	public function getReserveHOL()
	{
		return $this->reserveHOL;
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
	 * @param int $numComputers
	 */
	public function setNumComputers($numComputers)
	{
		$this->numComputers = $numComputers;
	}
	
	/**
	 * @return int $numComputers
	 */
	public function getNumComputers()
	{
		return $this->numComputers;
	}
	
}
		
