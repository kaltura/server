<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServTspAccountType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $tollFreeCallInNumber;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $tollCallInNumber;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $subscriberAccessCode;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $participantAccessCode;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'tollFreeCallInNumber':
				return 'WebexXml';
	
			case 'tollCallInNumber':
				return 'WebexXml';
	
			case 'subscriberAccessCode':
				return 'WebexXml';
	
			case 'participantAccessCode':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'tollFreeCallInNumber',
			'tollCallInNumber',
			'subscriberAccessCode',
			'participantAccessCode',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'tollFreeCallInNumber',
			'tollCallInNumber',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'tspAccountType';
	}
	
	/**
	 * @param WebexXml $tollFreeCallInNumber
	 */
	public function setTollFreeCallInNumber(WebexXml $tollFreeCallInNumber)
	{
		$this->tollFreeCallInNumber = $tollFreeCallInNumber;
	}
	
	/**
	 * @return WebexXml $tollFreeCallInNumber
	 */
	public function getTollFreeCallInNumber()
	{
		return $this->tollFreeCallInNumber;
	}
	
	/**
	 * @param WebexXml $tollCallInNumber
	 */
	public function setTollCallInNumber(WebexXml $tollCallInNumber)
	{
		$this->tollCallInNumber = $tollCallInNumber;
	}
	
	/**
	 * @return WebexXml $tollCallInNumber
	 */
	public function getTollCallInNumber()
	{
		return $this->tollCallInNumber;
	}
	
	/**
	 * @param WebexXml $subscriberAccessCode
	 */
	public function setSubscriberAccessCode(WebexXml $subscriberAccessCode)
	{
		$this->subscriberAccessCode = $subscriberAccessCode;
	}
	
	/**
	 * @return WebexXml $subscriberAccessCode
	 */
	public function getSubscriberAccessCode()
	{
		return $this->subscriberAccessCode;
	}
	
	/**
	 * @param WebexXml $participantAccessCode
	 */
	public function setParticipantAccessCode(WebexXml $participantAccessCode)
	{
		$this->participantAccessCode = $participantAccessCode;
	}
	
	/**
	 * @return WebexXml $participantAccessCode
	 */
	public function getParticipantAccessCode()
	{
		return $this->participantAccessCode;
	}
	
}
		
