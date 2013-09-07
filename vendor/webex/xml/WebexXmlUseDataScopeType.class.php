<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseDataScopeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $regDateStart;
	
	/**
	 *
	 * @var long
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var string
	 */
	protected $regDateEnd;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'regDateStart':
				return 'string';
	
			case 'timeZoneID':
				return 'long';
	
			case 'regDateEnd':
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
			'regDateStart',
			'timeZoneID',
			'regDateEnd',
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
		return 'dataScopeType';
	}
	
	/**
	 * @param string $regDateStart
	 */
	public function setRegDateStart($regDateStart)
	{
		$this->regDateStart = $regDateStart;
	}
	
	/**
	 * @return string $regDateStart
	 */
	public function getRegDateStart()
	{
		return $this->regDateStart;
	}
	
	/**
	 * @param long $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @return long $timeZoneID
	 */
	public function getTimeZoneID()
	{
		return $this->timeZoneID;
	}
	
	/**
	 * @param string $regDateEnd
	 */
	public function setRegDateEnd($regDateEnd)
	{
		$this->regDateEnd = $regDateEnd;
	}
	
	/**
	 * @return string $regDateEnd
	 */
	public function getRegDateEnd()
	{
		return $this->regDateEnd;
	}
	
}
		
