<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListTrainingsessionHistory.class.php');
require_once(__DIR__ . '/WebexXmlHistoryStartTimeScopeType.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryOrderTCHisType.class.php');
require_once(__DIR__ . '/WebexXmlHistoryEndTimeScopeType.class.php');
require_once(__DIR__ . '/WebexXmlComPsoFieldsType.class.php');

class WebexXmlListTrainingsessionHistoryRequest extends WebexXmlRequestBodyContent
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
	protected $confName;
	
	/**
	 *
	 * @var WebexXmlHistoryStartTimeScopeType
	 */
	protected $startTimeScope;
	
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlHistoryOrderTCHisType
	 */
	protected $order;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
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
	
	/**
	 *
	 * @var boolean
	 */
	protected $returnPSOFields;
	
	/**
	 *
	 * @var WebexXmlComPsoFieldsType
	 */
	protected $psoFields;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionKey',
			'confName',
			'startTimeScope',
			'listControl',
			'order',
			'hostWebExID',
			'endTimeScope',
			'confID',
			'timeZoneID',
			'returnPSOFields',
			'psoFields',
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
		return 'history:lsttrainingsessionHistory';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListTrainingsessionHistory';
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
	 * @param WebexXmlHistoryStartTimeScopeType $startTimeScope
	 */
	public function setStartTimeScope(WebexXmlHistoryStartTimeScopeType $startTimeScope)
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
	 * @param WebexXmlHistoryOrderTCHisType $order
	 */
	public function setOrder(WebexXmlHistoryOrderTCHisType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param string $hostWebExID
	 */
	public function setHostWebExID($hostWebExID)
	{
		$this->hostWebExID = $hostWebExID;
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
	
	/**
	 * @param boolean $returnPSOFields
	 */
	public function setReturnPSOFields($returnPSOFields)
	{
		$this->returnPSOFields = $returnPSOFields;
	}
	
	/**
	 * @param WebexXmlComPsoFieldsType $psoFields
	 */
	public function setPsoFields(WebexXmlComPsoFieldsType $psoFields)
	{
		$this->psoFields = $psoFields;
	}
	
}
		
