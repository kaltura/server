<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListEventattendeeHistory.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryOrderEcAttenHisType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryStartTimeScopeType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryEndTimeScopeType.class.php');

class WebexXmlListEventattendeeHistoryRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlHistoryOrderEcAttenHisType
	 */
	protected $order;
	
	/**
	 *
	 * @var WebexXmlHistoryStartTimeScopeType
	 */
	protected $startTimeScope;
	
	/**
	 *
	 * @var WebexXmlHistoryEndTimeScopeType
	 */
	protected $endTimeScope;
	
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var string
	 */
	protected $confName;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var boolean
	 */
	protected $attendeeStats;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'listControl',
			'order',
			'startTimeScope',
			'endTimeScope',
			'sessionKey',
			'confName',
			'confID',
			'attendeeStats',
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
		return 'history:lsteventattendeeHistory';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListEventattendeeHistory';
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlHistoryOrderEcAttenHisType $order
	 */
	public function setOrder(WebexXmlHistoryOrderEcAttenHisType $order)
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
	 * @param WebexXmlHistoryEndTimeScopeType $endTimeScope
	 */
	public function setEndTimeScope(WebexXmlHistoryEndTimeScopeType $endTimeScope)
	{
		$this->endTimeScope = $endTimeScope;
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param string $confName
	 */
	public function setConfName($confName)
	{
		$this->confName = $confName;
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
	/**
	 * @param boolean $attendeeStats
	 */
	public function setAttendeeStats($attendeeStats)
	{
		$this->attendeeStats = $attendeeStats;
	}
	
}
		
