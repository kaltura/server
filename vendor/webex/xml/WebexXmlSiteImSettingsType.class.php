<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteImSettingsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $attendeeInviteOther;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'attendeeInviteOther':
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
			'attendeeInviteOther',
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
		return 'imSettingsType';
	}
	
	/**
	 * @param boolean $attendeeInviteOther
	 */
	public function setAttendeeInviteOther($attendeeInviteOther)
	{
		$this->attendeeInviteOther = $attendeeInviteOther;
	}
	
	/**
	 * @return boolean $attendeeInviteOther
	 */
	public function getAttendeeInviteOther()
	{
		return $this->attendeeInviteOther;
	}
	
}
		
