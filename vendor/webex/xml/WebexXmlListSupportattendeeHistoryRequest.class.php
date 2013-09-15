<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListSupportattendeeHistory.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryOrderScAttenHisType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryStartTimeScopeType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryEndTimeScopeType.class.php');

class WebexXmlListSupportattendeeHistoryRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlHistoryOrderScAttenHisType
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
	protected $confID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionKey',
			'listControl',
			'order',
			'startTimeScope',
			'endTimeScope',
			'confID',
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
		return 'history:lstsupportattendeeHistory';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListSupportattendeeHistory';
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlHistoryOrderScAttenHisType $order
	 */
	public function setOrder(WebexXmlHistoryOrderScAttenHisType $order)
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
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
}
		
