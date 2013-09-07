<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteTSRouting extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $TSPrimaryName;
	
	/**
	 *
	 * @var string
	 */
	protected $TSPrimaryCountryCode;
	
	/**
	 *
	 * @var string
	 */
	protected $TSPrimaryNumber;
	
	/**
	 *
	 * @var int
	 */
	protected $TSDelay;
	
	/**
	 *
	 * @var string
	 */
	protected $TSSecondName;
	
	/**
	 *
	 * @var string
	 */
	protected $TSSecondCountryCode;
	
	/**
	 *
	 * @var string
	 */
	protected $TSSecondNumber;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'TSPrimaryName':
				return 'string';
	
			case 'TSPrimaryCountryCode':
				return 'string';
	
			case 'TSPrimaryNumber':
				return 'string';
	
			case 'TSDelay':
				return 'int';
	
			case 'TSSecondName':
				return 'string';
	
			case 'TSSecondCountryCode':
				return 'string';
	
			case 'TSSecondNumber':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'TSPrimaryName',
			'TSPrimaryCountryCode',
			'TSPrimaryNumber',
			'TSDelay',
			'TSSecondName',
			'TSSecondCountryCode',
			'TSSecondNumber',
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
		return 'TSRouting';
	}
	
	/**
	 * @param string $TSPrimaryName
	 */
	public function setTSPrimaryName($TSPrimaryName)
	{
		$this->TSPrimaryName = $TSPrimaryName;
	}
	
	/**
	 * @return string $TSPrimaryName
	 */
	public function getTSPrimaryName()
	{
		return $this->TSPrimaryName;
	}
	
	/**
	 * @param string $TSPrimaryCountryCode
	 */
	public function setTSPrimaryCountryCode($TSPrimaryCountryCode)
	{
		$this->TSPrimaryCountryCode = $TSPrimaryCountryCode;
	}
	
	/**
	 * @return string $TSPrimaryCountryCode
	 */
	public function getTSPrimaryCountryCode()
	{
		return $this->TSPrimaryCountryCode;
	}
	
	/**
	 * @param string $TSPrimaryNumber
	 */
	public function setTSPrimaryNumber($TSPrimaryNumber)
	{
		$this->TSPrimaryNumber = $TSPrimaryNumber;
	}
	
	/**
	 * @return string $TSPrimaryNumber
	 */
	public function getTSPrimaryNumber()
	{
		return $this->TSPrimaryNumber;
	}
	
	/**
	 * @param int $TSDelay
	 */
	public function setTSDelay($TSDelay)
	{
		$this->TSDelay = $TSDelay;
	}
	
	/**
	 * @return int $TSDelay
	 */
	public function getTSDelay()
	{
		return $this->TSDelay;
	}
	
	/**
	 * @param string $TSSecondName
	 */
	public function setTSSecondName($TSSecondName)
	{
		$this->TSSecondName = $TSSecondName;
	}
	
	/**
	 * @return string $TSSecondName
	 */
	public function getTSSecondName()
	{
		return $this->TSSecondName;
	}
	
	/**
	 * @param string $TSSecondCountryCode
	 */
	public function setTSSecondCountryCode($TSSecondCountryCode)
	{
		$this->TSSecondCountryCode = $TSSecondCountryCode;
	}
	
	/**
	 * @return string $TSSecondCountryCode
	 */
	public function getTSSecondCountryCode()
	{
		return $this->TSSecondCountryCode;
	}
	
	/**
	 * @param string $TSSecondNumber
	 */
	public function setTSSecondNumber($TSSecondNumber)
	{
		$this->TSSecondNumber = $TSSecondNumber;
	}
	
	/**
	 * @return string $TSSecondNumber
	 */
	public function getTSSecondNumber()
	{
		return $this->TSSecondNumber;
	}
	
}
		
