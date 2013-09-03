<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlDelSessionTemplates.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlUseSessionTemplateType.class.php');
require_once(__DIR__ . '/WebexXmlUseTemplateTypeType.class.php');

class WebexXmlDelSessionTemplatesRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlUseSessionTemplateType>
	 */
	protected $sessionTemplate;
	
	/**
	 *
	 * @var WebexXmlUseTemplateTypeType
	 */
	protected $templateType;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionTemplate',
			'templateType',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'sessionTemplate',
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
		return 'use:delSessionTemplates';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlDelSessionTemplates';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlUseSessionTemplateType> $sessionTemplate
	 */
	public function setSessionTemplate(WebexXmlArray $sessionTemplate)
	{
		if($sessionTemplate->getType() != 'WebexXmlUseSessionTemplateType')
			throw new WebexXmlException(get_class($this) . "::sessionTemplate must be of type WebexXmlUseSessionTemplateType");
		
		$this->sessionTemplate = $sessionTemplate;
	}
	
	/**
	 * @param WebexXmlUseTemplateTypeType $templateType
	 */
	public function setTemplateType(WebexXmlUseTemplateTypeType $templateType)
	{
		$this->templateType = $templateType;
	}
	
}
		
