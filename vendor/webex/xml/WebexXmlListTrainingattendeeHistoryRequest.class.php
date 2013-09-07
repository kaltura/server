<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListTrainingattendeeHistory.class.php');
require_once(__DIR__ . '/WebexXmlHistoryOrderTCAttenHisType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryStartTimeScopeType.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryEndTimeScopeType.class.php');

class WebexXmlListTrainingattendeeHistoryRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var WebexXmlHistoryOrderTCAttenHisType
	 */
	protected $order;
	
	/**
	 *
	 * @var WebexXmlHistoryStartTimeScopeType
	 */
	protected $startTimeScope;
	
	/**
	 *
	 * @var string
	 */
	protected $confName;
	
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var WebexXmlHistoryEndTimeScopeType
	 */
	protected $endTimeScope;
	
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
			'order',
			'startTimeScope',
			'confName',
			'listControl',
			'confID',
			'endTimeScope',
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
		return 'history:lsttrainingattendeeHistory';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListTrainingattendeeHistory';
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param WebexXmlHistoryOrderTCAttenHisType $order
	 */
	public function setOrder(WebexXmlHistoryOrderTCAttenHisType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param WebexXmlHistoryStartTimeScopeType $startTimeScope
	 */
	public function setStartTimeScope(WebexXmlHistoryStartTimeScopeType $startTimeScope)
	{
		$this->startTimeScope = $startTimeScope;
	}
	
	/**
	 * @param string $confName
	 */
	public function setConfName($confName)
	{
		$this->confName = $confName;
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
	/**
	 * @param WebexXmlHistoryEndTimeScopeType $endTimeScope
	 */
	public function setEndTimeScope(WebexXmlHistoryEndTimeScopeType $endTimeScope)
	{
		$this->endTimeScope = $endTimeScope;
	}
	
	/**
	 * @param int $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
}
		
