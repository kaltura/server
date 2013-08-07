<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2ContextType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $name;
	
	/**
	 *
	 * @var WebexXmlQtiGeneric_identifierType
	 */
	protected $generic_identifier;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'WebexXml';
	
			case 'generic_identifier':
				return 'WebexXmlQtiGeneric_identifierType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'name',
			'generic_identifier',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'name',
			'generic_identifier',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'contextType';
	}
	
	/**
	 * @param WebexXml $name
	 */
	public function setName(WebexXml $name)
	{
		$this->name = $name;
	}
	
	/**
	 * @return WebexXml $name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param WebexXmlQtiGeneric_identifierType $generic_identifier
	 */
	public function setGeneric_identifier(WebexXmlQtiGeneric_identifierType $generic_identifier)
	{
		$this->generic_identifier = $generic_identifier;
	}
	
	/**
	 * @return WebexXmlQtiGeneric_identifierType $generic_identifier
	 */
	public function getGeneric_identifier()
	{
		return $this->generic_identifier;
	}
	
}
		
