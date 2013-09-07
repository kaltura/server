<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpOcMetaDataType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlEpSessionTemplateType
	 */
	protected $sessionTemplate;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'sessionTemplate':
				return 'WebexXmlEpSessionTemplateType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionTemplate',
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
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'ocMetaDataType';
	}
	
	/**
	 * @param WebexXmlEpSessionTemplateType $sessionTemplate
	 */
	public function setSessionTemplate(WebexXmlEpSessionTemplateType $sessionTemplate)
	{
		$this->sessionTemplate = $sessionTemplate;
	}
	
	/**
	 * @return WebexXmlEpSessionTemplateType $sessionTemplate
	 */
	public function getSessionTemplate()
	{
		return $this->sessionTemplate;
	}
	
}
		
