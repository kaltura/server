<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteEventCenterType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $standardFields;
	
	/**
	 *
	 * @var WebexXmlEventCustomFieldsType
	 */
	protected $customFields;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'standardFields':
				return 'WebexXml';
	
			case 'customFields':
				return 'WebexXmlEventCustomFieldsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'standardFields',
			'customFields',
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
		return 'eventCenterType';
	}
	
	/**
	 * @param WebexXml $standardFields
	 */
	public function setStandardFields(WebexXml $standardFields)
	{
		$this->standardFields = $standardFields;
	}
	
	/**
	 * @return WebexXml $standardFields
	 */
	public function getStandardFields()
	{
		return $this->standardFields;
	}
	
	/**
	 * @param WebexXmlEventCustomFieldsType $customFields
	 */
	public function setCustomFields(WebexXmlEventCustomFieldsType $customFields)
	{
		$this->customFields = $customFields;
	}
	
	/**
	 * @return WebexXmlEventCustomFieldsType $customFields
	 */
	public function getCustomFields()
	{
		return $this->customFields;
	}
	
}
		
