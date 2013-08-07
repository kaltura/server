<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlReserveLab.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlReserveLabRequest extends WebexXmlRequestBodyContent
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
	protected $numComputers;
	
	/**
	 *
	 * @var string
	 */
	protected $topic;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $startDate;
	
	/**
	 *
	 * @var string
	 */
	protected $endDate;
	
	/**
	 *
	 * @var int
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $timeLimit;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sendMail;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'labName',
			'numComputers',
			'topic',
			'startDate',
			'endDate',
			'timeZoneID',
			'timeLimit',
			'sendMail',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'labName',
			'numComputers',
			'topic',
			'startDate',
			'endDate',
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
		return 'trainingsession:reserveLab';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlReserveLab';
	}
	
	/**
	 * @param string $labName
	 */
	public function setLabName($labName)
	{
		$this->labName = $labName;
	}
	
	/**
	 * @param int $numComputers
	 */
	public function setNumComputers($numComputers)
	{
		$this->numComputers = $numComputers;
	}
	
	/**
	 * @param string $topic
	 */
	public function setTopic($topic)
	{
		$this->topic = $topic;
	}
	
	/**
	 * @param WebexXml $startDate
	 */
	public function setStartDate(WebexXml $startDate)
	{
		$this->startDate = $startDate;
	}
	
	/**
	 * @param string $endDate
	 */
	public function setEndDate($endDate)
	{
		$this->endDate = $endDate;
	}
	
	/**
	 * @param int $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @param WebexXml $timeLimit
	 */
	public function setTimeLimit(WebexXml $timeLimit)
	{
		$this->timeLimit = $timeLimit;
	}
	
	/**
	 * @param boolean $sendMail
	 */
	public function setSendMail($sendMail)
	{
		$this->sendMail = $sendMail;
	}
	
}
		
