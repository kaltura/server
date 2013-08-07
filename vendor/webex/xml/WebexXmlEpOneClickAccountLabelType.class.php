<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpOneClickAccountLabelType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $participantLimitedAccessCodeLabel;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'participantLimitedAccessCodeLabel':
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
			'participantLimitedAccessCodeLabel',
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
		return 'oneClickAccountLabelType';
	}
	
	/**
	 * @param WebexXml $participantLimitedAccessCodeLabel
	 */
	public function setParticipantLimitedAccessCodeLabel(WebexXml $participantLimitedAccessCodeLabel)
	{
		$this->participantLimitedAccessCodeLabel = $participantLimitedAccessCodeLabel;
	}
	
	/**
	 * @return WebexXml $participantLimitedAccessCodeLabel
	 */
	public function getParticipantLimitedAccessCodeLabel()
	{
		return $this->participantLimitedAccessCodeLabel;
	}
	
}
		
