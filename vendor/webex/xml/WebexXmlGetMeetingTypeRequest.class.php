<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlGetMeetingType.class.php');

class WebexXmlGetMeetingTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var integer
	 */
	protected $meetingTypeID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'meetingTypeID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'meetingTypeID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'meetingtype';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'meetingtype:getMeetingType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlGetMeetingType';
	}
	
	/**
	 * @param integer $meetingTypeID
	 */
	public function setMeetingTypeID($meetingTypeID)
	{
		$this->meetingTypeID = $meetingTypeID;
	}
	
}
		
