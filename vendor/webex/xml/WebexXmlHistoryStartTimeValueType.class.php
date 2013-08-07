<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistoryStartTimeValueType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $sessionStartTimeStart;
	
	/**
	 *
	 * @var string
	 */
	protected $sessionStartTimeEnd;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'sessionStartTimeStart':
				return 'string';
	
			case 'sessionStartTimeEnd':
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
			'sessionStartTimeStart',
			'sessionStartTimeEnd',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'sessionStartTimeStart',
			'sessionStartTimeEnd',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'startTimeValueType';
	}
	
	/**
	 * @param string $sessionStartTimeStart
	 */
	public function setSessionStartTimeStart($sessionStartTimeStart)
	{
		$this->sessionStartTimeStart = $sessionStartTimeStart;
	}
	
	/**
	 * @return string $sessionStartTimeStart
	 */
	public function getSessionStartTimeStart()
	{
		return $this->sessionStartTimeStart;
	}
	
	/**
	 * @param string $sessionStartTimeEnd
	 */
	public function setSessionStartTimeEnd($sessionStartTimeEnd)
	{
		$this->sessionStartTimeEnd = $sessionStartTimeEnd;
	}
	
	/**
	 * @return string $sessionStartTimeEnd
	 */
	public function getSessionStartTimeEnd()
	{
		return $this->sessionStartTimeEnd;
	}
	
}

