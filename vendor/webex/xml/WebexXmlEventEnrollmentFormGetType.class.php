<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventEnrollmentFormGetType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlEventStandardFieldsInstanceType
	 */
	protected $standardFields;
	
	/**
	 *
	 * @var WebexXmlEventCustomFieldsInstanceType
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
				return 'WebexXmlEventStandardFieldsInstanceType';
	
			case 'customFields':
				return 'WebexXmlEventCustomFieldsInstanceType';
	
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
		return 'enrollmentFormGetType';
	}
	
	/**
	 * @param WebexXmlEventStandardFieldsInstanceType $standardFields
	 */
	public function setStandardFields(WebexXmlEventStandardFieldsInstanceType $standardFields)
	{
		$this->standardFields = $standardFields;
	}
	
	/**
	 * @return WebexXmlEventStandardFieldsInstanceType $standardFields
	 */
	public function getStandardFields()
	{
		return $this->standardFields;
	}
	
	/**
	 * @param WebexXmlEventCustomFieldsInstanceType $customFields
	 */
	public function setCustomFields(WebexXmlEventCustomFieldsInstanceType $customFields)
	{
		$this->customFields = $customFields;
	}
	
	/**
	 * @return WebexXmlEventCustomFieldsInstanceType $customFields
	 */
	public function getCustomFields()
	{
		return $this->customFields;
	}
	
}
		
