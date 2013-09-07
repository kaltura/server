<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListSummaryMeeting.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlMeetOrderType.class.php');
require_once(__DIR__ . '/WebexXmlMeetDateScopeType.class.php');

class WebexXmlListSummaryMeetingRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlMeetOrderType
	 */
	protected $order;
	
	/**
	 *
	 * @var WebexXmlMeetDateScopeType
	 */
	protected $dateScope;
	
	/**
	 *
	 * @var long
	 */
	protected $meetingKey;
	
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
			'listControl',
			'order',
			'dateScope',
			'meetingKey',
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
		return 'meeting';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'meeting:lstsummaryMeeting';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListSummaryMeeting';
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlMeetOrderType $order
	 */
	public function setOrder(WebexXmlMeetOrderType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param WebexXmlMeetDateScopeType $dateScope
	 */
	public function setDateScope(WebexXmlMeetDateScopeType $dateScope)
	{
		$this->dateScope = $dateScope;
	}
	
	/**
	 * @param long $meetingKey
	 */
	public function setMeetingKey($meetingKey)
	{
		$this->meetingKey = $meetingKey;
	}
	
	/**
	 * @param string $hostWebExID
	 */
	public function setHostWebExID($hostWebExID)
	{
		$this->hostWebExID = $hostWebExID;
	}
	
}
		
