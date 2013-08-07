<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSessionOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $defaultSessionType;
	
	/**
	 *
	 * @var WebexXmlComServiceTypeType
	 */
	protected $defaultServiceType;
	
	/**
	 *
	 * @var boolean
	 */
	protected $autoDeleteAfterMeetingEnd;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayQuickStartHost;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayQuickStartAttendees;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'defaultSessionType':
				return 'integer';
	
			case 'defaultServiceType':
				return 'WebexXmlComServiceTypeType';
	
			case 'autoDeleteAfterMeetingEnd':
				return 'boolean';
	
			case 'displayQuickStartHost':
				return 'boolean';
	
			case 'displayQuickStartAttendees':
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
			'defaultSessionType',
			'defaultServiceType',
			'autoDeleteAfterMeetingEnd',
			'displayQuickStartHost',
			'displayQuickStartAttendees',
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
		return 'sessionOptionsType';
	}
	
	/**
	 * @param integer $defaultSessionType
	 */
	public function setDefaultSessionType($defaultSessionType)
	{
		$this->defaultSessionType = $defaultSessionType;
	}
	
	/**
	 * @return integer $defaultSessionType
	 */
	public function getDefaultSessionType()
	{
		return $this->defaultSessionType;
	}
	
	/**
	 * @param WebexXmlComServiceTypeType $defaultServiceType
	 */
	public function setDefaultServiceType(WebexXmlComServiceTypeType $defaultServiceType)
	{
		$this->defaultServiceType = $defaultServiceType;
	}
	
	/**
	 * @return WebexXmlComServiceTypeType $defaultServiceType
	 */
	public function getDefaultServiceType()
	{
		return $this->defaultServiceType;
	}
	
	/**
	 * @param boolean $autoDeleteAfterMeetingEnd
	 */
	public function setAutoDeleteAfterMeetingEnd($autoDeleteAfterMeetingEnd)
	{
		$this->autoDeleteAfterMeetingEnd = $autoDeleteAfterMeetingEnd;
	}
	
	/**
	 * @return boolean $autoDeleteAfterMeetingEnd
	 */
	public function getAutoDeleteAfterMeetingEnd()
	{
		return $this->autoDeleteAfterMeetingEnd;
	}
	
	/**
	 * @param boolean $displayQuickStartHost
	 */
	public function setDisplayQuickStartHost($displayQuickStartHost)
	{
		$this->displayQuickStartHost = $displayQuickStartHost;
	}
	
	/**
	 * @return boolean $displayQuickStartHost
	 */
	public function getDisplayQuickStartHost()
	{
		return $this->displayQuickStartHost;
	}
	
	/**
	 * @param boolean $displayQuickStartAttendees
	 */
	public function setDisplayQuickStartAttendees($displayQuickStartAttendees)
	{
		$this->displayQuickStartAttendees = $displayQuickStartAttendees;
	}
	
	/**
	 * @return boolean $displayQuickStartAttendees
	 */
	public function getDisplayQuickStartAttendees()
	{
		return $this->displayQuickStartAttendees;
	}
	
}
		
