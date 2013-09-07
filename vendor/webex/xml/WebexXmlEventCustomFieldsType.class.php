<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventCustomFieldsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventTextboxType>
	 */
	protected $textBox;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventCheckboxGroupType>
	 */
	protected $checkBoxGroup;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventChoiceGroupType>
	 */
	protected $optionButtonGroup;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventChoiceGroupType>
	 */
	protected $dropDownGroup;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'textBox':
				return 'WebexXmlArray<WebexXmlEventTextboxType>';
	
			case 'checkBoxGroup':
				return 'WebexXmlArray<WebexXmlEventCheckboxGroupType>';
	
			case 'optionButtonGroup':
				return 'WebexXmlArray<WebexXmlEventChoiceGroupType>';
	
			case 'dropDownGroup':
				return 'WebexXmlArray<WebexXmlEventChoiceGroupType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'textBox',
			'checkBoxGroup',
			'optionButtonGroup',
			'dropDownGroup',
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
		return 'customFieldsType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlEventTextboxType> $textBox
	 */
	public function setTextBox(WebexXmlArray $textBox)
	{
		if($textBox->getType() != 'WebexXmlEventTextboxType')
			throw new WebexXmlException(get_class($this) . "::textBox must be of type WebexXmlEventTextboxType");
		
		$this->textBox = $textBox;
	}
	
	/**
	 * @return WebexXmlArray $textBox
	 */
	public function getTextBox()
	{
		return $this->textBox;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlEventCheckboxGroupType> $checkBoxGroup
	 */
	public function setCheckBoxGroup(WebexXmlArray $checkBoxGroup)
	{
		if($checkBoxGroup->getType() != 'WebexXmlEventCheckboxGroupType')
			throw new WebexXmlException(get_class($this) . "::checkBoxGroup must be of type WebexXmlEventCheckboxGroupType");
		
		$this->checkBoxGroup = $checkBoxGroup;
	}
	
	/**
	 * @return WebexXmlArray $checkBoxGroup
	 */
	public function getCheckBoxGroup()
	{
		return $this->checkBoxGroup;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlEventChoiceGroupType> $optionButtonGroup
	 */
	public function setOptionButtonGroup(WebexXmlArray $optionButtonGroup)
	{
		if($optionButtonGroup->getType() != 'WebexXmlEventChoiceGroupType')
			throw new WebexXmlException(get_class($this) . "::optionButtonGroup must be of type WebexXmlEventChoiceGroupType");
		
		$this->optionButtonGroup = $optionButtonGroup;
	}
	
	/**
	 * @return WebexXmlArray $optionButtonGroup
	 */
	public function getOptionButtonGroup()
	{
		return $this->optionButtonGroup;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlEventChoiceGroupType> $dropDownGroup
	 */
	public function setDropDownGroup(WebexXmlArray $dropDownGroup)
	{
		if($dropDownGroup->getType() != 'WebexXmlEventChoiceGroupType')
			throw new WebexXmlException(get_class($this) . "::dropDownGroup must be of type WebexXmlEventChoiceGroupType");
		
		$this->dropDownGroup = $dropDownGroup;
	}
	
	/**
	 * @return WebexXmlArray $dropDownGroup
	 */
	public function getDropDownGroup()
	{
		return $this->dropDownGroup;
	}
	
}
		
