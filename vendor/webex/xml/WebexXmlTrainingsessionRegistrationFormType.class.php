<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionRegistrationFormType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlTrainStandardFieldsType
	 */
	protected $standardFields;
	
	/**
	 *
	 * @var WebexXmlTrainCustomFieldsType
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
				return 'WebexXmlTrainStandardFieldsType';
	
			case 'customFields':
				return 'WebexXmlTrainCustomFieldsType';
	
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
		return 'registrationFormType';
	}
	
	/**
	 * @param WebexXmlTrainStandardFieldsType $standardFields
	 */
	public function setStandardFields(WebexXmlTrainStandardFieldsType $standardFields)
	{
		$this->standardFields = $standardFields;
	}
	
	/**
	 * @return WebexXmlTrainStandardFieldsType $standardFields
	 */
	public function getStandardFields()
	{
		return $this->standardFields;
	}
	
	/**
	 * @param WebexXmlTrainCustomFieldsType $customFields
	 */
	public function setCustomFields(WebexXmlTrainCustomFieldsType $customFields)
	{
		$this->customFields = $customFields;
	}
	
	/**
	 * @return WebexXmlTrainCustomFieldsType $customFields
	 */
	public function getCustomFields()
	{
		return $this->customFields;
	}
	
}
		
