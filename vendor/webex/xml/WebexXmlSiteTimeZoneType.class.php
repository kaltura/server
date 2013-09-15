<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteTimeZoneType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var integer
	 */
	protected $gmtOffset;
	
	/**
	 *
	 * @var string
	 */
	protected $description;
	
	/**
	 *
	 * @var string
	 */
	protected $shortName;
	
	/**
	 *
	 * @var boolean
	 */
	protected $hideTimeZoneName;
	
	/**
	 *
	 * @var boolean
	 */
	protected $fallInDST;
	
	/**
	 *
	 * @var string
	 */
	protected $standardLabel;
	
	/**
	 *
	 * @var string
	 */
	protected $daylightLabel;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'timeZoneID':
				return 'integer';
	
			case 'gmtOffset':
				return 'integer';
	
			case 'description':
				return 'string';
	
			case 'shortName':
				return 'string';
	
			case 'hideTimeZoneName':
				return 'boolean';
	
			case 'fallInDST':
				return 'boolean';
	
			case 'standardLabel':
				return 'string';
	
			case 'daylightLabel':
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
			'timeZoneID',
			'gmtOffset',
			'description',
			'shortName',
			'hideTimeZoneName',
			'fallInDST',
			'standardLabel',
			'daylightLabel',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'timeZoneID',
			'gmtOffset',
			'description',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'timeZoneType';
	}
	
	/**
	 * @param integer $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @return integer $timeZoneID
	 */
	public function getTimeZoneID()
	{
		return $this->timeZoneID;
	}
	
	/**
	 * @param integer $gmtOffset
	 */
	public function setGmtOffset($gmtOffset)
	{
		$this->gmtOffset = $gmtOffset;
	}
	
	/**
	 * @return integer $gmtOffset
	 */
	public function getGmtOffset()
	{
		return $this->gmtOffset;
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
	 * @param string $shortName
	 */
	public function setShortName($shortName)
	{
		$this->shortName = $shortName;
	}
	
	/**
	 * @return string $shortName
	 */
	public function getShortName()
	{
		return $this->shortName;
	}
	
	/**
	 * @param boolean $hideTimeZoneName
	 */
	public function setHideTimeZoneName($hideTimeZoneName)
	{
		$this->hideTimeZoneName = $hideTimeZoneName;
	}
	
	/**
	 * @return boolean $hideTimeZoneName
	 */
	public function getHideTimeZoneName()
	{
		return $this->hideTimeZoneName;
	}
	
	/**
	 * @param boolean $fallInDST
	 */
	public function setFallInDST($fallInDST)
	{
		$this->fallInDST = $fallInDST;
	}
	
	/**
	 * @return boolean $fallInDST
	 */
	public function getFallInDST()
	{
		return $this->fallInDST;
	}
	
	/**
	 * @param string $standardLabel
	 */
	public function setStandardLabel($standardLabel)
	{
		$this->standardLabel = $standardLabel;
	}
	
	/**
	 * @return string $standardLabel
	 */
	public function getStandardLabel()
	{
		return $this->standardLabel;
	}
	
	/**
	 * @param string $daylightLabel
	 */
	public function setDaylightLabel($daylightLabel)
	{
		$this->daylightLabel = $daylightLabel;
	}
	
	/**
	 * @return string $daylightLabel
	 */
	public function getDaylightLabel()
	{
		return $this->daylightLabel;
	}
	
}

