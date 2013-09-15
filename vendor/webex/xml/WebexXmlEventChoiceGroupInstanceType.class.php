<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventChoiceGroupInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $fieldID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'fieldID':
				return 'long';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'fieldID',
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
		return 'choiceGroupInstanceType';
	}
	
	/**
	 * @param long $fieldID
	 */
	public function setFieldID($fieldID)
	{
		$this->fieldID = $fieldID;
	}
	
	/**
	 * @return long $fieldID
	 */
	public function getFieldID()
	{
		return $this->fieldID;
	}
	
}
		
