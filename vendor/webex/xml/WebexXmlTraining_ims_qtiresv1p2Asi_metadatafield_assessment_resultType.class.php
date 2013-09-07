<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2Asi_metadatafield_assessment_resultType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $field_name;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $field_value;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'field_name':
				return 'WebexXml';
	
			case 'field_value':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'field_name',
			'field_value',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'field_name',
			'field_value',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'asi_metadatafield_assessment_resultType';
	}
	
	/**
	 * @param WebexXml $field_name
	 */
	public function setField_name(WebexXml $field_name)
	{
		$this->field_name = $field_name;
	}
	
	/**
	 * @return WebexXml $field_name
	 */
	public function getField_name()
	{
		return $this->field_name;
	}
	
	/**
	 * @param WebexXml $field_value
	 */
	public function setField_value(WebexXml $field_value)
	{
		$this->field_value = $field_value;
	}
	
	/**
	 * @return WebexXml $field_value
	 */
	public function getField_value()
	{
		return $this->field_value;
	}
	
}
		
