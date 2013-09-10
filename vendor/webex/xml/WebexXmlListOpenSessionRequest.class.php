<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListOpenSession.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlComServiceTypeType.class.php');

class WebexXmlListOpenSessionRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlComServiceTypeType>
	 */
	protected $serviceType;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'serviceType',
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
		return 'ep:lstOpenSession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListOpenSession';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlComServiceTypeType> $serviceType
	 */
	public function setServiceType(WebexXmlArray $serviceType)
	{
		if($serviceType->getType() != 'WebexXmlComServiceTypeType')
			throw new WebexXmlException(get_class($this) . "::serviceType must be of type WebexXmlComServiceTypeType");
		
		$this->serviceType = $serviceType;
	}
	
}
		
