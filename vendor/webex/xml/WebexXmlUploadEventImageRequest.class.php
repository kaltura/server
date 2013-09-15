<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlUploadEventImage.class.php');
require_once(__DIR__ . '/WebexXmlEventImageTypeType.class.php');

class WebexXmlUploadEventImageRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var WebexXmlEventImageTypeType
	 */
	protected $imageType;
	
	/**
	 *
	 * @var base64Binary
	 */
	protected $imageData;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionKey',
			'imageType',
			'imageData',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'sessionKey',
			'imageType',
			'imageData',
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
		return 'event:uploadEventImage';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlUploadEventImage';
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param WebexXmlEventImageTypeType $imageType
	 */
	public function setImageType(WebexXmlEventImageTypeType $imageType)
	{
		$this->imageType = $imageType;
	}
	
	/**
	 * @param base64Binary $imageData
	 */
	public function setImageData($imageData)
	{
		$this->imageData = $imageData;
	}
	
}
		
