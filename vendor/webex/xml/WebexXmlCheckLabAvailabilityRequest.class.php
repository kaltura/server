<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlCheckLabAvailability.class.php');

class WebexXmlCheckLabAvailabilityRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $labName;
	
	/**
	 *
	 * @var int
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var string
	 */
	protected $sessionStartTime;
	
	/**
	 *
	 * @var string
	 */
	protected $sessionEndTime;
	
	/**
	 *
	 * @var int
	 */
	protected $numComputers;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'labName',
			'timeZoneID',
			'sessionStartTime',
			'sessionEndTime',
			'numComputers',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'labName',
			'timeZoneID',
			'sessionStartTime',
			'sessionEndTime',
			'numComputers',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'trainingsession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'trainingsession:checkLabAvailability';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlCheckLabAvailability';
	}
	
	/**
	 * @param string $labName
	 */
	public function setLabName($labName)
	{
		$this->labName = $labName;
	}
	
	/**
	 * @param int $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @param string $sessionStartTime
	 */
	public function setSessionStartTime($sessionStartTime)
	{
		$this->sessionStartTime = $sessionStartTime;
	}
	
	/**
	 * @param string $sessionEndTime
	 */
	public function setSessionEndTime($sessionEndTime)
	{
		$this->sessionEndTime = $sessionEndTime;
	}
	
	/**
	 * @param int $numComputers
	 */
	public function setNumComputers($numComputers)
	{
		$this->numComputers = $numComputers;
	}
	
}
		
