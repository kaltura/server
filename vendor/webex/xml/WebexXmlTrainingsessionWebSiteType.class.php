<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionWebSiteType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $startDate;
	
	/**
	 *
	 * @var string
	 */
	protected $dueDate;
	
	/**
	 *
	 * @var WebexXmlTrainEmailAttendeeType
	 */
	protected $emailAttendee;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'startDate':
				return 'WebexXml';
	
			case 'dueDate':
				return 'string';
	
			case 'emailAttendee':
				return 'WebexXmlTrainEmailAttendeeType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'startDate',
			'dueDate',
			'emailAttendee',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'startDate',
			'dueDate',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'webSiteType';
	}
	
	/**
	 * @param WebexXml $startDate
	 */
	public function setStartDate(WebexXml $startDate)
	{
		$this->startDate = $startDate;
	}
	
	/**
	 * @return WebexXml $startDate
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}
	
	/**
	 * @param string $dueDate
	 */
	public function setDueDate($dueDate)
	{
		$this->dueDate = $dueDate;
	}
	
	/**
	 * @return string $dueDate
	 */
	public function getDueDate()
	{
		return $this->dueDate;
	}
	
	/**
	 * @param WebexXmlTrainEmailAttendeeType $emailAttendee
	 */
	public function setEmailAttendee(WebexXmlTrainEmailAttendeeType $emailAttendee)
	{
		$this->emailAttendee = $emailAttendee;
	}
	
	/**
	 * @return WebexXmlTrainEmailAttendeeType $emailAttendee
	 */
	public function getEmailAttendee()
	{
		return $this->emailAttendee;
	}
	
}
		
