<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionRegistrationFormInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlTrainStandardFieldsType
	 */
	protected $standardFields;
	
	/**
	 *
	 * @var WebexXmlTrainCustomFieldsInstanceType
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
				return 'WebexXmlTrainCustomFieldsInstanceType';
	
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
		return 'registrationFormInstanceType';
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
	 * @param WebexXmlTrainCustomFieldsInstanceType $customFields
	 */
	public function setCustomFields(WebexXmlTrainCustomFieldsInstanceType $customFields)
	{
		$this->customFields = $customFields;
	}
	
	/**
	 * @return WebexXmlTrainCustomFieldsInstanceType $customFields
	 */
	public function getCustomFields()
	{
		return $this->customFields;
	}
	
}
		
