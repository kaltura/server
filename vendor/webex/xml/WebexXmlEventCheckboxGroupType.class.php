<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventCheckboxGroupType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $label;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventCheckboxType>
	 */
	protected $checkBox;
	
	/**
	 *
	 * @var integer
	 */
	protected $index;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'label':
				return 'WebexXml';
	
			case 'checkBox':
				return 'WebexXmlArray<WebexXmlEventCheckboxType>';
	
			case 'index':
				return 'integer';
	
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
			'checkBox',
			'index',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'label',
			'checkBox',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'checkboxGroupType';
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
	
	/**
	 * @param WebexXmlArray<WebexXmlEventCheckboxType> $checkBox
	 */
	public function setCheckBox(WebexXmlArray $checkBox)
	{
		if($checkBox->getType() != 'WebexXmlEventCheckboxType')
			throw new WebexXmlException(get_class($this) . "::checkBox must be of type WebexXmlEventCheckboxType");
		
		$this->checkBox = $checkBox;
	}
	
	/**
	 * @return WebexXmlArray $checkBox
	 */
	public function getCheckBox()
	{
		return $this->checkBox;
	}
	
	/**
	 * @param integer $index
	 */
	public function setIndex($index)
	{
		$this->index = $index;
	}
	
	/**
	 * @return integer $index
	 */
	public function getIndex()
	{
		return $this->index;
	}
	
}
		
