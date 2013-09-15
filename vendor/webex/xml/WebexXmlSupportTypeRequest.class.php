<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSupportType.class.php');
require_once(__DIR__ . '/WebexXmlScMetaDataType.class.php');
require_once(__DIR__ . '/WebexXmlComTrackingType.class.php');

class WebexXmlSupportTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlScMetaDataType
	 */
	protected $metaData;
	
	/**
	 *
	 * @var WebexXmlComTrackingType
	 */
	protected $tracking;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'metaData',
			'tracking',
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
		return 'supportsession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'supportsession:supportType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSupportType';
	}
	
	/**
	 * @param WebexXmlScMetaDataType $metaData
	 */
	public function setMetaData(WebexXmlScMetaDataType $metaData)
	{
		$this->metaData = $metaData;
	}
	
	/**
	 * @param WebexXmlComTrackingType $tracking
	 */
	public function setTracking(WebexXmlComTrackingType $tracking)
	{
		$this->tracking = $tracking;
	}
	
}
		
