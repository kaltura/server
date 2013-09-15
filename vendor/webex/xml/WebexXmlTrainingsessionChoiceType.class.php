<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionChoiceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $label;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'label':
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
			'label',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'label',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'choiceType';
	}
	
	/**
	 * @param WebexXml $label
	 */
	public function setLabel(WebexXml $label)
	{
		$this->label = $label;
	}
	
	/**
	 * @return WebexXml $label
	 */
	public function getLabel()
	{
		return $this->label;
	}
	
}
		
