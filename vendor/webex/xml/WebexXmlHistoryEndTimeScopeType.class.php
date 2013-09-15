<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistoryEndTimeScopeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $sessionEndTimeStart;
	
	/**
	 *
	 * @var string
	 */
	protected $sessionEndTimeEnd;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'sessionEndTimeStart':
				return 'string';
	
			case 'sessionEndTimeEnd':
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
			'sessionEndTimeStart',
			'sessionEndTimeEnd',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'sessionEndTimeStart',
			'sessionEndTimeEnd',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'endTimeScopeType';
	}
	
	/**
	 * @param string $sessionEndTimeStart
	 */
	public function setSessionEndTimeStart($sessionEndTimeStart)
	{
		$this->sessionEndTimeStart = $sessionEndTimeStart;
	}
	
	/**
	 * @return string $sessionEndTimeStart
	 */
	public function getSessionEndTimeStart()
	{
		return $this->sessionEndTimeStart;
	}
	
	/**
	 * @param string $sessionEndTimeEnd
	 */
	public function setSessionEndTimeEnd($sessionEndTimeEnd)
	{
		$this->sessionEndTimeEnd = $sessionEndTimeEnd;
	}
	
	/**
	 * @return string $sessionEndTimeEnd
	 */
	public function getSessionEndTimeEnd()
	{
		return $this->sessionEndTimeEnd;
	}
	
}
		
