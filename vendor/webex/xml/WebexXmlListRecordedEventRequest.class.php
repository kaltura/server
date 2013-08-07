<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListRecordedEvent.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlEventOrderType.class.php');
require_once(__DIR__ . '/WebexXmlEventDateScopeType.class.php');

class WebexXmlListRecordedEventRequest extends WebexXmlRequestBodyContent
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
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var long
	 */
	protected $programID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'listControl',
			'order',
			'dateScope',
			'hostWebExID',
			'programID',
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
		return 'event:lstrecordedEvent';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListRecordedEvent';
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
	
}
		
