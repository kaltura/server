<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListSummarySession.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlEpOrderType.class.php');
require_once(__DIR__ . '/WebexXmlEpDateScopeType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/integer.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlComServiceTypeType.class.php');
require_once(__DIR__ . '/WebexXmlEpStatusType.class.php');

class WebexXmlListSummarySessionRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlEpOrderType
	 */
	protected $order;
	
	/**
	 *
	 * @var WebexXmlEpDateScopeType
	 */
	protected $dateScope;
	
	/**
	 *
	 * @var WebexXmlArray<integer>
	 */
	protected $sessionTypes;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlComServiceTypeType>
	 */
	protected $serviceTypes;
	
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
	 * @var string
	 */
	protected $hostEmail;
	
	/**
	 *
	 * @var WebexXmlEpStatusType
	 */
	protected $status;
	
	/**
	 *
	 * @var boolean
	 */
	protected $recurrence;
	
	/**
	 *
	 * @var boolean
	 */
	protected $invited;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var string
	 */
	protected $confName;
	
	/**
	 *
	 * @var boolean
	 */
	protected $inclAudioOnly;
	
	/**
	 *
	 * @var boolean
	 */
	protected $returnPSOFields;
	
	/**
	 *
	 * @var boolean
	 */
	protected $returnAssistFields;
	
	/**
	 *
	 * @var boolean
	 */
	protected $returnTCSingleRecurrence;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'listControl',
			'order',
			'dateScope',
			'sessionTypes',
			'serviceTypes',
			'sessionKey',
			'hostWebExID',
			'hostEmail',
			'status',
			'recurrence',
			'invited',
			'confID',
			'confName',
			'inclAudioOnly',
			'returnPSOFields',
			'returnAssistFields',
			'returnTCSingleRecurrence',
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
		return 'ep';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'ep:lstsummarySession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListSummarySession';
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlEpOrderType $order
	 */
	public function setOrder(WebexXmlEpOrderType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param WebexXmlEpDateScopeType $dateScope
	 */
	public function setDateScope(WebexXmlEpDateScopeType $dateScope)
	{
		$this->dateScope = $dateScope;
	}
	
	/**
	 * @param WebexXmlArray<integer> $sessionTypes
	 */
	public function setSessionTypes(WebexXmlArray $sessionTypes)
	{
		if($sessionTypes->getType() != 'integer')
			throw new WebexXmlException(get_class($this) . "::sessionTypes must be of type integer");
		
		$this->sessionTypes = $sessionTypes;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlComServiceTypeType> $serviceTypes
	 */
	public function setServiceTypes(WebexXmlArray $serviceTypes)
	{
		if($serviceTypes->getType() != 'WebexXmlComServiceTypeType')
			throw new WebexXmlException(get_class($this) . "::serviceTypes must be of type WebexXmlComServiceTypeType");
		
		$this->serviceTypes = $serviceTypes;
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
	 * @param string $hostEmail
	 */
	public function setHostEmail($hostEmail)
	{
		$this->hostEmail = $hostEmail;
	}
	
	/**
	 * @param WebexXmlEpStatusType $status
	 */
	public function setStatus(WebexXmlEpStatusType $status)
	{
		$this->status = $status;
	}
	
	/**
	 * @param boolean $recurrence
	 */
	public function setRecurrence($recurrence)
	{
		$this->recurrence = $recurrence;
	}
	
	/**
	 * @param boolean $invited
	 */
	public function setInvited($invited)
	{
		$this->invited = $invited;
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
	/**
	 * @param string $confName
	 */
	public function setConfName($confName)
	{
		$this->confName = $confName;
	}
	
	/**
	 * @param boolean $inclAudioOnly
	 */
	public function setInclAudioOnly($inclAudioOnly)
	{
		$this->inclAudioOnly = $inclAudioOnly;
	}
	
	/**
	 * @param boolean $returnPSOFields
	 */
	public function setReturnPSOFields($returnPSOFields)
	{
		$this->returnPSOFields = $returnPSOFields;
	}
	
	/**
	 * @param boolean $returnAssistFields
	 */
	public function setReturnAssistFields($returnAssistFields)
	{
		$this->returnAssistFields = $returnAssistFields;
	}
	
	/**
	 * @param boolean $returnTCSingleRecurrence
	 */
	public function setReturnTCSingleRecurrence($returnTCSingleRecurrence)
	{
		$this->returnTCSingleRecurrence = $returnTCSingleRecurrence;
	}
	
}
		
