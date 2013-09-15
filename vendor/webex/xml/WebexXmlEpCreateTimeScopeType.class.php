<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpCreateTimeScopeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $createTimeStart;
	
	/**
	 *
	 * @var string
	 */
	protected $createTimeEnd;
	
	/**
	 *
	 * @var int
	 */
	protected $timeZoneID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'createTimeStart':
				return 'string';
	
			case 'createTimeEnd':
				return 'string';
	
			case 'timeZoneID':
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
			'createTimeStart',
			'createTimeEnd',
			'timeZoneID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'createTimeStart',
			'createTimeEnd',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'createTimeScopeType';
	}
	
	/**
	 * @param string $createTimeStart
	 */
	public function setCreateTimeStart($createTimeStart)
	{
		$this->createTimeStart = $createTimeStart;
	}
	
	/**
	 * @return string $createTimeStart
	 */
	public function getCreateTimeStart()
	{
		return $this->createTimeStart;
	}
	
	/**
	 * @param string $createTimeEnd
	 */
	public function setCreateTimeEnd($createTimeEnd)
	{
		$this->createTimeEnd = $createTimeEnd;
	}
	
	/**
	 * @return string $createTimeEnd
	 */
	public function getCreateTimeEnd()
	{
		return $this->createTimeEnd;
	}
	
	/**
	 * @param int $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @return int $timeZoneID
	 */
	public function getTimeZoneID()
	{
		return $this->timeZoneID;
	}
	
}
		
