<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventCustomFieldsInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventTextboxInstanceType>
	 */
	protected $textBox;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventCheckboxGroupInstanceType>
	 */
	protected $checkBoxGroup;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventChoiceGroupInstanceType>
	 */
	protected $optionButtonGroup;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventChoiceGroupInstanceType>
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
				return 'WebexXmlArray<WebexXmlEventTextboxInstanceType>';
	
			case 'checkBoxGroup':
				return 'WebexXmlArray<WebexXmlEventCheckboxGroupInstanceType>';
	
			case 'optionButtonGroup':
				return 'WebexXmlArray<WebexXmlEventChoiceGroupInstanceType>';
	
			case 'dropDownGroup':
				return 'WebexXmlArray<WebexXmlEventChoiceGroupInstanceType>';
	
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
		return 'customFieldsInstanceType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlEventTextboxInstanceType> $textBox
	 */
	public function setTextBox(WebexXmlArray $textBox)
	{
		if($textBox->getType() != 'WebexXmlEventTextboxInstanceType')
			throw new WebexXmlException(get_class($this) . "::textBox must be of type WebexXmlEventTextboxInstanceType");
		
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
	 * @param WebexXmlArray<WebexXmlEventCheckboxGroupInstanceType> $checkBoxGroup
	 */
	public function setCheckBoxGroup(WebexXmlArray $checkBoxGroup)
	{
		if($checkBoxGroup->getType() != 'WebexXmlEventCheckboxGroupInstanceType')
			throw new WebexXmlException(get_class($this) . "::checkBoxGroup must be of type WebexXmlEventCheckboxGroupInstanceType");
		
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
	 * @param WebexXmlArray<WebexXmlEventChoiceGroupInstanceType> $optionButtonGroup
	 */
	public function setOptionButtonGroup(WebexXmlArray $optionButtonGroup)
	{
		if($optionButtonGroup->getType() != 'WebexXmlEventChoiceGroupInstanceType')
			throw new WebexXmlException(get_class($this) . "::optionButtonGroup must be of type WebexXmlEventChoiceGroupInstanceType");
		
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
	 * @param WebexXmlArray<WebexXmlEventChoiceGroupInstanceType> $dropDownGroup
	 */
	public function setDropDownGroup(WebexXmlArray $dropDownGroup)
	{
		if($dropDownGroup->getType() != 'WebexXmlEventChoiceGroupInstanceType')
			throw new WebexXmlException(get_class($this) . "::dropDownGroup must be of type WebexXmlEventChoiceGroupInstanceType");
		
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
		
