<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListTimeZone.class.php');
require_once(__DIR__ . '/integer.class.php');

class WebexXmlListTimeZoneRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<integer>
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var string
	 */
	protected $date;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'timeZoneID',
			'date',
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
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'site';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'site:lstTimeZone';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListTimeZone';
	}
	
	/**
	 * @param WebexXmlArray<integer> $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		if($timeZoneID->getType() != 'integer')
			throw new WebexXmlException(get_class($this) . "::timeZoneID must be of type integer");
		
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @param string $date
	 */
	public function setDate($date)
	{
		$this->date = $date;
	}
	
}
		
