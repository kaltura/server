<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistorySalesAttendeeHistoryInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $account;
	
	/**
	 *
	 * @var string
	 */
	protected $opportunity;
	
	/**
	 *
	 * @var WebexXmlHistoryAttendeeTypeType
	 */
	protected $attendeeType;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'account':
				return 'string';
	
			case 'opportunity':
				return 'string';
	
			case 'attendeeType':
				return 'WebexXmlHistoryAttendeeTypeType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'account',
			'opportunity',
			'attendeeType',
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
		return 'salesAttendeeHistoryInstanceType';
	}
	
	/**
	 * @param string $account
	 */
	public function setAccount($account)
	{
		$this->account = $account;
	}
	
	/**
	 * @return string $account
	 */
	public function getAccount()
	{
		return $this->account;
	}
	
	/**
	 * @param string $opportunity
	 */
	public function setOpportunity($opportunity)
	{
		$this->opportunity = $opportunity;
	}
	
	/**
	 * @return string $opportunity
	 */
	public function getOpportunity()
	{
		return $this->opportunity;
	}
	
	/**
	 * @param WebexXmlHistoryAttendeeTypeType $attendeeType
	 */
	public function setAttendeeType(WebexXmlHistoryAttendeeTypeType $attendeeType)
	{
		$this->attendeeType = $attendeeType;
	}
	
	/**
	 * @return WebexXmlHistoryAttendeeTypeType $attendeeType
	 */
	public function getAttendeeType()
	{
		return $this->attendeeType;
	}
	
}
		
