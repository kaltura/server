<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServTspAccountLabelType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $tollFreeCallInNumberLabel;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $tollCallInNumberLabel;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $subscriberAccessCodeLabel;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $participantAccessCodeLabel;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'tollFreeCallInNumberLabel':
				return 'WebexXml';
	
			case 'tollCallInNumberLabel':
				return 'WebexXml';
	
			case 'subscriberAccessCodeLabel':
				return 'WebexXml';
	
			case 'participantAccessCodeLabel':
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
			'tollFreeCallInNumberLabel',
			'tollCallInNumberLabel',
			'subscriberAccessCodeLabel',
			'participantAccessCodeLabel',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'tollFreeCallInNumberLabel',
			'tollCallInNumberLabel',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'tspAccountLabelType';
	}
	
	/**
	 * @param WebexXml $tollFreeCallInNumberLabel
	 */
	public function setTollFreeCallInNumberLabel(WebexXml $tollFreeCallInNumberLabel)
	{
		$this->tollFreeCallInNumberLabel = $tollFreeCallInNumberLabel;
	}
	
	/**
	 * @return WebexXml $tollFreeCallInNumberLabel
	 */
	public function getTollFreeCallInNumberLabel()
	{
		return $this->tollFreeCallInNumberLabel;
	}
	
	/**
	 * @param WebexXml $tollCallInNumberLabel
	 */
	public function setTollCallInNumberLabel(WebexXml $tollCallInNumberLabel)
	{
		$this->tollCallInNumberLabel = $tollCallInNumberLabel;
	}
	
	/**
	 * @return WebexXml $tollCallInNumberLabel
	 */
	public function getTollCallInNumberLabel()
	{
		return $this->tollCallInNumberLabel;
	}
	
	/**
	 * @param WebexXml $subscriberAccessCodeLabel
	 */
	public function setSubscriberAccessCodeLabel(WebexXml $subscriberAccessCodeLabel)
	{
		$this->subscriberAccessCodeLabel = $subscriberAccessCodeLabel;
	}
	
	/**
	 * @return WebexXml $subscriberAccessCodeLabel
	 */
	public function getSubscriberAccessCodeLabel()
	{
		return $this->subscriberAccessCodeLabel;
	}
	
	/**
	 * @param WebexXml $participantAccessCodeLabel
	 */
	public function setParticipantAccessCodeLabel(WebexXml $participantAccessCodeLabel)
	{
		$this->participantAccessCodeLabel = $participantAccessCodeLabel;
	}
	
	/**
	 * @return WebexXml $participantAccessCodeLabel
	 */
	public function getParticipantAccessCodeLabel()
	{
		return $this->participantAccessCodeLabel;
	}
	
}
		
