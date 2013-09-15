<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListSummaryTrainingSession.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlTrainOrderType.class.php');
require_once(__DIR__ . '/WebexXmlSessDateScopeType.class.php');

class WebexXmlListSummaryTrainingSessionRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlTrainOrderType
	 */
	protected $order;
	
	/**
	 *
	 * @var WebexXmlSessDateScopeType
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
		return 'trainingsession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'trainingsession:lstsummaryTrainingSession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListSummaryTrainingSession';
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlTrainOrderType $order
	 */
	public function setOrder(WebexXmlTrainOrderType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param WebexXmlSessDateScopeType $dateScope
	 */
	public function setDateScope(WebexXmlSessDateScopeType $dateScope)
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
	
}
		
