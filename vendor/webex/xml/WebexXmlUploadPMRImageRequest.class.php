<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlUploadPMRImage.class.php');
require_once(__DIR__ . '/WebexXmlUseImageForType.class.php');

class WebexXmlUploadPMRImageRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlUseImageForType
	 */
	protected $imageFor;
	
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
			'imageFor',
			'imageData',
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
		return 'use';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'use:uploadPMRImage';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlUploadPMRImage';
	}
	
	/**
	 * @param WebexXmlUseImageForType $imageFor
	 */
	public function setImageFor(WebexXmlUseImageForType $imageFor)
	{
		$this->imageFor = $imageFor;
	}
	
	/**
	 * @param base64Binary $imageData
	 */
	public function setImageData($imageData)
	{
		$this->imageData = $imageData;
	}
	
}
		
