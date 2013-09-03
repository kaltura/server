<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSupportedServicesType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $meetingCenter;
	
	/**
	 *
	 * @var boolean
	 */
	protected $trainingCenter;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportCenter;
	
	/**
	 *
	 * @var boolean
	 */
	protected $eventCenter;
	
	/**
	 *
	 * @var boolean
	 */
	protected $salesCenter;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'meetingCenter':
				return 'boolean';
	
			case 'trainingCenter':
				return 'boolean';
	
			case 'supportCenter':
				return 'boolean';
	
			case 'eventCenter':
				return 'boolean';
	
			case 'salesCenter':
				return 'boolean';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'meetingCenter',
			'trainingCenter',
			'supportCenter',
			'eventCenter',
			'salesCenter',
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
		return 'supportedServicesType';
	}
	
	/**
	 * @param boolean $meetingCenter
	 */
	public function setMeetingCenter($meetingCenter)
	{
		$this->meetingCenter = $meetingCenter;
	}
	
	/**
	 * @return boolean $meetingCenter
	 */
	public function getMeetingCenter()
	{
		return $this->meetingCenter;
	}
	
	/**
	 * @param boolean $trainingCenter
	 */
	public function setTrainingCenter($trainingCenter)
	{
		$this->trainingCenter = $trainingCenter;
	}
	
	/**
	 * @return boolean $trainingCenter
	 */
	public function getTrainingCenter()
	{
		return $this->trainingCenter;
	}
	
	/**
	 * @param boolean $supportCenter
	 */
	public function setSupportCenter($supportCenter)
	{
		$this->supportCenter = $supportCenter;
	}
	
	/**
	 * @return boolean $supportCenter
	 */
	public function getSupportCenter()
	{
		return $this->supportCenter;
	}
	
	/**
	 * @param boolean $eventCenter
	 */
	public function setEventCenter($eventCenter)
	{
		$this->eventCenter = $eventCenter;
	}
	
	/**
	 * @return boolean $eventCenter
	 */
	public function getEventCenter()
	{
		return $this->eventCenter;
	}
	
	/**
	 * @param boolean $salesCenter
	 */
	public function setSalesCenter($salesCenter)
	{
		$this->salesCenter = $salesCenter;
	}
	
	/**
	 * @return boolean $salesCenter
	 */
	public function getSalesCenter()
	{
		return $this->salesCenter;
	}
	
}
		
