<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListRecordaccessHistory.class.php');
require_once(__DIR__ . '/WebexXmlHistoryCreationTimeScopeType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryViewTimeScopeType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryOrderRecAccHisType.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');

class WebexXmlListRecordaccessHistoryRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlHistoryCreationTimeScopeType
	 */
	protected $creationTimeScope;
	
	/**
	 *
	 * @var WebexXmlHistoryViewTimeScopeType
	 */
	protected $viewTimeScope;
	
	/**
	 *
	 * @var WebexXmlHistoryOrderRecAccHisType
	 */
	protected $order;
	
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
	 * @var string
	 */
	protected $recordName;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'creationTimeScope',
			'viewTimeScope',
			'order',
			'listControl',
			'timeZoneID',
			'recordName',
			'hostWebExID',
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
		return 'history:lstrecordaccessHistory';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListRecordaccessHistory';
	}
	
	/**
	 * @param WebexXmlHistoryCreationTimeScopeType $creationTimeScope
	 */
	public function setCreationTimeScope(WebexXmlHistoryCreationTimeScopeType $creationTimeScope)
	{
		$this->creationTimeScope = $creationTimeScope;
	}
	
	/**
	 * @param WebexXmlHistoryViewTimeScopeType $viewTimeScope
	 */
	public function setViewTimeScope(WebexXmlHistoryViewTimeScopeType $viewTimeScope)
	{
		$this->viewTimeScope = $viewTimeScope;
	}
	
	/**
	 * @param WebexXmlHistoryOrderRecAccHisType $order
	 */
	public function setOrder(WebexXmlHistoryOrderRecAccHisType $order)
	{
		$this->order = $order;
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
	 * @param string $recordName
	 */
	public function setRecordName($recordName)
	{
		$this->recordName = $recordName;
	}
	
	/**
	 * @param string $hostWebExID
	 */
	public function setHostWebExID($hostWebExID)
	{
		$this->hostWebExID = $hostWebExID;
	}
	
}
		
