<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventEnrollmentFormSetType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlEventStandardFieldsType
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
				return 'WebexXmlEventStandardFieldsType';
	
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
		return 'enrollmentFormSetType';
	}
	
	/**
	 * @param WebexXmlEventStandardFieldsType $standardFields
	 */
	public function setStandardFields(WebexXmlEventStandardFieldsType $standardFields)
	{
		$this->standardFields = $standardFields;
	}
	
	/**
	 * @return WebexXmlEventStandardFieldsType $standardFields
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
		
