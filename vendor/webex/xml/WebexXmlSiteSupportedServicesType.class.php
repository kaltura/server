<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteSupportedServicesType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlSiteSupportedServiceType
	 */
	protected $meetingCenter;
	
	/**
	 *
	 * @var WebexXmlSiteSupportedServiceType
	 */
	protected $trainingCenter;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $supportCenter;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $eventCenter;
	
	/**
	 *
	 * @var WebexXmlSiteSupportedServiceType
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
				return 'WebexXmlSiteSupportedServiceType';
	
			case 'trainingCenter':
				return 'WebexXmlSiteSupportedServiceType';
	
			case 'supportCenter':
				return 'WebexXml';
	
			case 'eventCenter':
				return 'WebexXml';
	
			case 'salesCenter':
				return 'WebexXmlSiteSupportedServiceType';
	
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
			'meetingCenter',
			'trainingCenter',
			'supportCenter',
			'eventCenter',
			'salesCenter',
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
	 * @param WebexXmlSiteSupportedServiceType $meetingCenter
	 */
	public function setMeetingCenter(WebexXmlSiteSupportedServiceType $meetingCenter)
	{
		$this->meetingCenter = $meetingCenter;
	}
	
	/**
	 * @return WebexXmlSiteSupportedServiceType $meetingCenter
	 */
	public function getMeetingCenter()
	{
		return $this->meetingCenter;
	}
	
	/**
	 * @param WebexXmlSiteSupportedServiceType $trainingCenter
	 */
	public function setTrainingCenter(WebexXmlSiteSupportedServiceType $trainingCenter)
	{
		$this->trainingCenter = $trainingCenter;
	}
	
	/**
	 * @return WebexXmlSiteSupportedServiceType $trainingCenter
	 */
	public function getTrainingCenter()
	{
		return $this->trainingCenter;
	}
	
	/**
	 * @param WebexXml $supportCenter
	 */
	public function setSupportCenter(WebexXml $supportCenter)
	{
		$this->supportCenter = $supportCenter;
	}
	
	/**
	 * @return WebexXml $supportCenter
	 */
	public function getSupportCenter()
	{
		return $this->supportCenter;
	}
	
	/**
	 * @param WebexXml $eventCenter
	 */
	public function setEventCenter(WebexXml $eventCenter)
	{
		$this->eventCenter = $eventCenter;
	}
	
	/**
	 * @return WebexXml $eventCenter
	 */
	public function getEventCenter()
	{
		return $this->eventCenter;
	}
	
	/**
	 * @param WebexXmlSiteSupportedServiceType $salesCenter
	 */
	public function setSalesCenter(WebexXmlSiteSupportedServiceType $salesCenter)
	{
		$this->salesCenter = $salesCenter;
	}
	
	/**
	 * @return WebexXmlSiteSupportedServiceType $salesCenter
	 */
	public function getSalesCenter()
	{
		return $this->salesCenter;
	}
	
}
		
