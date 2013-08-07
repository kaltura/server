<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListRecordaccessDetailHistory.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');

class WebexXmlListRecordaccessDetailHistoryRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $recordID;
	
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var int
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var boolean
	 */
	protected $returnRegFields;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'recordID',
			'listControl',
			'timeZoneID',
			'returnRegFields',
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
		return 'history';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'history:lstrecordaccessDetailHistory';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListRecordaccessDetailHistory';
	}
	
	/**
	 * @param long $recordID
	 */
	public function setRecordID($recordID)
	{
		$this->recordID = $recordID;
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param int $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @param boolean $returnRegFields
	 */
	public function setReturnRegFields($returnRegFields)
	{
		$this->returnRegFields = $returnRegFields;
	}
	
}
		
