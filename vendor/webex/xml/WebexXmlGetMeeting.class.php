<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlGetMeeting extends WebexXmlObject
{
	/**
	 *
	 * @var string
	 */
	protected $hostKey;
	
	/**
	 *
	 * @var long
	 */
	protected $eventID;
	
	/**
	 *
	 * @var string
	 */
	protected $guestToken;
	
	/**
	 *
	 * @var string
	 */
	protected $hostType;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'hostKey':
				return 'string';
	
			case 'eventID':
				return 'long';
	
			case 'guestToken':
				return 'string';
	
			case 'hostType':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $hostKey
	 */
	public function getHostKey()
	{
		return $this->hostKey;
	}
	
	/**
	 * @return long $eventID
	 */
	public function getEventID()
	{
		return $this->eventID;
	}
	
	/**
	 * @return string $guestToken
	 */
	public function getGuestToken()
	{
		return $this->guestToken;
	}
	
	/**
	 * @return string $hostType
	 */
	public function getHostType()
	{
		return $this->hostType;
	}
	
}
		
