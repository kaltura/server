<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListSupportsessionHistory.class.php');
require_once(__DIR__ . '/WebexXmlHistoryStartTimeValueType.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryOrderScHisType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryEndTimeScopeType.class.php');

class WebexXmlListSupportsessionHistoryRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var WebexXmlHistoryStartTimeValueType
	 */
	protected $startTimeScope;
	
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlHistoryOrderScHisType
	 */
	protected $order;
	
	/**
	 *
	 * @var WebexXmlHistoryEndTimeScopeType
	 */
	protected $endTimeScope;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var int
	 */
	protected $timeZoneID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionKey',
			'hostWebExID',
			'startTimeScope',
			'listControl',
			'order',
			'endTimeScope',
			'confID',
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
		return 'history:lstsupportsessionHistory';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListSupportsessionHistory';
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param string $hostWebExID
	 */
	public function setHostWebExID($hostWebExID)
	{
		$this->hostWebExID = $hostWebExID;
	}
	
	/**
	 * @param WebexXmlHistoryStartTimeValueType $startTimeScope
	 */
	public function setStartTimeScope(WebexXmlHistoryStartTimeValueType $startTimeScope)
	{
		$this->startTimeScope = $startTimeScope;
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlHistoryOrderScHisType $order
	 */
	public function setOrder(WebexXmlHistoryOrderScHisType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param WebexXmlHistoryEndTimeScopeType $endTimeScope
	 */
	public function setEndTimeScope(WebexXmlHistoryEndTimeScopeType $endTimeScope)
	{
		$this->endTimeScope = $endTimeScope;
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
	/**
	 * @param int $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
}
		
