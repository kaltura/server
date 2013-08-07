<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlEpEnableOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlMeetTelephonyType.class.php');

class WebexXmlGetJoinSessionInfo extends WebexXmlObject
{
	/**
	 *
	 * @var long
	 */
	protected $siteID;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var string
	 */
	protected $confName;
	
	/**
	 *
	 * @var long
	 */
	protected $attendeeID;
	
	/**
	 *
	 * @var string
	 */
	protected $mzmAddress;
	
	/**
	 *
	 * @var string
	 */
	protected $mccAddress;
	
	/**
	 *
	 * @var WebexXmlEpEnableOptionsType
	 */
	protected $enableOptions;
	
	/**
	 *
	 * @var WebexXmlMeetTelephonyType
	 */
	protected $telephony;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'siteID':
				return 'long';
	
			case 'confID':
				return 'long';
	
			case 'confName':
				return 'string';
	
			case 'attendeeID':
				return 'long';
	
			case 'mzmAddress':
				return 'string';
	
			case 'mccAddress':
				return 'string';
	
			case 'enableOptions':
				return 'WebexXmlEpEnableOptionsType';
	
			case 'telephony':
				return 'WebexXmlMeetTelephonyType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return long $siteID
	 */
	public function getSiteID()
	{
		return $this->siteID;
	}
	
	/**
	 * @return long $confID
	 */
	public function getConfID()
	{
		return $this->confID;
	}
	
	/**
	 * @return string $confName
	 */
	public function getConfName()
	{
		return $this->confName;
	}
	
	/**
	 * @return long $attendeeID
	 */
	public function getAttendeeID()
	{
		return $this->attendeeID;
	}
	
	/**
	 * @return string $mzmAddress
	 */
	public function getMzmAddress()
	{
		return $this->mzmAddress;
	}
	
	/**
	 * @return string $mccAddress
	 */
	public function getMccAddress()
	{
		return $this->mccAddress;
	}
	
	/**
	 * @return WebexXmlEpEnableOptionsType $enableOptions
	 */
	public function getEnableOptions()
	{
		return $this->enableOptions;
	}
	
	/**
	 * @return WebexXmlMeetTelephonyType $telephony
	 */
	public function getTelephony()
	{
		return $this->telephony;
	}
	
}
		
