<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistoryStartTimeRangeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $startTimeStart;
	
	/**
	 *
	 * @var string
	 */
	protected $startTimeEnd;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'startTimeStart':
				return 'string';
	
			case 'startTimeEnd':
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
			'startTimeStart',
			'startTimeEnd',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'startTimeStart',
			'startTimeEnd',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'startTimeRangeType';
	}
	
	/**
	 * @param string $startTimeStart
	 */
	public function setStartTimeStart($startTimeStart)
	{
		$this->startTimeStart = $startTimeStart;
	}
	
	/**
	 * @return string $startTimeStart
	 */
	public function getStartTimeStart()
	{
		return $this->startTimeStart;
	}
	
	/**
	 * @param string $startTimeEnd
	 */
	public function setStartTimeEnd($startTimeEnd)
	{
		$this->startTimeEnd = $startTimeEnd;
	}
	
	/**
	 * @return string $startTimeEnd
	 */
	public function getStartTimeEnd()
	{
		return $this->startTimeEnd;
	}
	
}

