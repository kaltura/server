<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSessionParticipantsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $maxUserNumber;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSessParticipantType>
	 */
	protected $participants;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'maxUserNumber':
				return 'integer';
	
			case 'participants':
				return 'WebexXmlArray<WebexXmlSessParticipantType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'maxUserNumber',
			'participants',
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
		return 'participantsType';
	}
	
	/**
	 * @param integer $maxUserNumber
	 */
	public function setMaxUserNumber($maxUserNumber)
	{
		$this->maxUserNumber = $maxUserNumber;
	}
	
	/**
	 * @return integer $maxUserNumber
	 */
	public function getMaxUserNumber()
	{
		return $this->maxUserNumber;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlSessParticipantType> $participants
	 */
	public function setParticipants(WebexXmlArray $participants)
	{
		if($participants->getType() != 'WebexXmlSessParticipantType')
			throw new WebexXmlException(get_class($this) . "::participants must be of type WebexXmlSessParticipantType");
		
		$this->participants = $participants;
	}
	
	/**
	 * @return WebexXmlArray $participants
	 */
	public function getParticipants()
	{
		return $this->participants;
	}
	
}
		
