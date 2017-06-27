<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListRecordingInRecycleBin.class.php');
require_once(__DIR__ . '/WebexXmlEpListControlType.class.php');
require_once(__DIR__ . '/WebexXmlEpCreateTimeScopeType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlComServiceTypeType.class.php');

class WebexXmlListRecordingInRecycleBinRequest extends WebexXmlRequestBodyContent
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
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var string
	 */
	protected $recordName;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'listControl',
			'createTimeScope',
			'sessionKey',
			'recordName',
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
		return 'ep:lstRecordingInRecycleBin';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListRecordingInRecycleBin';
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
		
