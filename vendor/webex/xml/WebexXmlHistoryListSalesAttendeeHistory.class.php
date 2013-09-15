<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistoryListSalesAttendeeHistory extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $timeZoneID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'timeZoneID':
				return 'integer';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'timeZoneID',
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
		return 'lstsalesAttendeeHistory';
	}
	
	/**
	 * @param integer $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @return integer $timeZoneID
	 */
	public function getTimeZoneID()
	{
		return $this->timeZoneID;
	}
	
}
		
