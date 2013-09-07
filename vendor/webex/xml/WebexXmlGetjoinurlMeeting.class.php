<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlGetjoinurlMeeting extends WebexXmlObject
{
	/**
	 *
	 * @var string
	 */
	protected $joinMeetingURL;
	
	/**
	 *
	 * @var string
	 */
	protected $inviteMeetingURL;
	
	/**
	 *
	 * @var string
	 */
	protected $registerMeetingURL;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'joinMeetingURL':
				return 'string';
	
			case 'inviteMeetingURL':
				return 'string';
	
			case 'registerMeetingURL':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $joinMeetingURL
	 */
	public function getJoinMeetingURL()
	{
		return $this->joinMeetingURL;
	}
	
	/**
	 * @return string $inviteMeetingURL
	 */
	public function getInviteMeetingURL()
	{
		return $this->inviteMeetingURL;
	}
	
	/**
	 * @return string $registerMeetingURL
	 */
	public function getRegisterMeetingURL()
	{
		return $this->registerMeetingURL;
	}
	
}
		
