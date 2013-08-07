<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListSummaryEvent.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlEventOrderType.class.php');
require_once(__DIR__ . '/WebexXmlEventDateScopeType.class.php');

class WebexXmlListSummaryEventRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlEventOrderType
	 */
	protected $order;
	
	/**
	 *
	 * @var WebexXmlEventDateScopeType
	 */
	protected $dateScope;
	
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
	 * @var long
	 */
	protected $programID;
	
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
			'dateScope',
			'sessionKey',
			'hostWebExID',
			'programID',
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
		return 'event';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'event:lstsummaryEvent';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListSummaryEvent';
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlEventOrderType $order
	 */
	public function setOrder(WebexXmlEventOrderType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param WebexXmlEventDateScopeType $dateScope
	 */
	public function setDateScope(WebexXmlEventDateScopeType $dateScope)
	{
		$this->dateScope = $dateScope;
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
	 * @param long $programID
	 */
	public function setProgramID($programID)
	{
		$this->programID = $programID;
	}
	
	/**
	 * @param boolean $attendeeStats
	 */
	public function setAttendeeStats($attendeeStats)
	{
		$this->attendeeStats = $attendeeStats;
	}
	
}
		
