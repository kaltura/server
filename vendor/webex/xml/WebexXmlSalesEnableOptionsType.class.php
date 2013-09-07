<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesEnableOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $autoDeleteAfterMeetingEnd;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sendEmailByClient;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayQuickStartHost;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'autoDeleteAfterMeetingEnd':
				return 'boolean';
	
			case 'sendEmailByClient':
				return 'boolean';
	
			case 'displayQuickStartHost':
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
			'autoDeleteAfterMeetingEnd',
			'sendEmailByClient',
			'displayQuickStartHost',
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
		return 'enableOptionsType';
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
	 * @param boolean $sendEmailByClient
	 */
	public function setSendEmailByClient($sendEmailByClient)
	{
		$this->sendEmailByClient = $sendEmailByClient;
	}
	
	/**
	 * @return boolean $sendEmailByClient
	 */
	public function getSendEmailByClient()
	{
		return $this->sendEmailByClient;
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
	
}
		
