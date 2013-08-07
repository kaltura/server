<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListMeetingType.class.php');
require_once(__DIR__ . '/integer.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');

class WebexXmlListMeetingTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<integer>
	 */
	protected $meetingTypeID;
	
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'meetingTypeID',
			'listControl',
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
		return 'meetingtype:lstMeetingType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListMeetingType';
	}
	
	/**
	 * @param WebexXmlArray<integer> $meetingTypeID
	 */
	public function setMeetingTypeID($meetingTypeID)
	{
		if($meetingTypeID->getType() != 'integer')
			throw new WebexXmlException(get_class($this) . "::meetingTypeID must be of type integer");
		
		$this->meetingTypeID = $meetingTypeID;
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
}
		
