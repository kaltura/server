<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListSummarySalesSession.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlSalesOrderType.class.php');
require_once(__DIR__ . '/WebexXmlSalesDateScopeType.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlListSummarySalesSessionRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlSalesOrderType
	 */
	protected $order;
	
	/**
	 *
	 * @var WebexXmlSalesDateScopeType
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
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $account;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $opportunity;
	
	
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
			'account',
			'opportunity',
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
		return 'sales';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'sales:lstsummarySalesSession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListSummarySalesSession';
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlSalesOrderType $order
	 */
	public function setOrder(WebexXmlSalesOrderType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param WebexXmlSalesDateScopeType $dateScope
	 */
	public function setDateScope(WebexXmlSalesDateScopeType $dateScope)
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
	
	/**
	 * @param WebexXml $account
	 */
	public function setAccount(WebexXml $account)
	{
		$this->account = $account;
	}
	
	/**
	 * @param WebexXml $opportunity
	 */
	public function setOpportunity(WebexXml $opportunity)
	{
		$this->opportunity = $opportunity;
	}
	
}
		
