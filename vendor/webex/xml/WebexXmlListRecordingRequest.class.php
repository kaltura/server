<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListRecording.class.php');
require_once(__DIR__ . '/WebexXmlEpListControlType.class.php');
require_once(__DIR__ . '/WebexXmlEpCreateTimeScopeType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlComServiceTypeType.class.php');

class WebexXmlListRecordingRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlEpListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlEpCreateTimeScopeType
	 */
	protected $createTimeScope;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var boolean
	 */
	protected $returnSessionDetails;
	
	/**
	 *
	 * @var string
	 */
	protected $recordName;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlComServiceTypeType>
	 */
	protected $serviceTypes;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'listControl',
			'createTimeScope',
			'hostWebExID',
			'sessionKey',
			'returnSessionDetails',
			'recordName',
			'serviceTypes',
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
		return 'ep:lstRecording';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListRecording';
	}
	
	/**
	 * @param WebexXmlEpListControlType $listControl
	 */
	public function setListControl(WebexXmlEpListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlEpCreateTimeScopeType $createTimeScope
	 */
	public function setCreateTimeScope(WebexXmlEpCreateTimeScopeType $createTimeScope)
	{
		$this->createTimeScope = $createTimeScope;
	}
	
	/**
	 * @param string $hostWebExID
	 */
	public function setHostWebExID($hostWebExID)
	{
		$this->hostWebExID = $hostWebExID;
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param boolean $returnSessionDetails
	 */
	public function setReturnSessionDetails($returnSessionDetails)
	{
		$this->returnSessionDetails = $returnSessionDetails;
	}
	
	/**
	 * @param string $recordName
	 */
	public function setRecordName($recordName)
	{
		$this->recordName = $recordName;
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
	
}
		
