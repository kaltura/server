<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionCustomFieldsInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainTextboxInstanceType>
	 */
	protected $textBox;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainCheckboxGroupInstanceType>
	 */
	protected $checkBoxGroup;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainChoiceGroupInstanceType>
	 */
	protected $optionButtonGroup;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainChoiceGroupInstanceType>
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
				return 'WebexXmlArray<WebexXmlTrainTextboxInstanceType>';
	
			case 'checkBoxGroup':
				return 'WebexXmlArray<WebexXmlTrainCheckboxGroupInstanceType>';
	
			case 'optionButtonGroup':
				return 'WebexXmlArray<WebexXmlTrainChoiceGroupInstanceType>';
	
			case 'dropDownGroup':
				return 'WebexXmlArray<WebexXmlTrainChoiceGroupInstanceType>';
	
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
	 * @param WebexXmlArray<WebexXmlTrainTextboxInstanceType> $textBox
	 */
	public function setTextBox(WebexXmlArray $textBox)
	{
		if($textBox->getType() != 'WebexXmlTrainTextboxInstanceType')
			throw new WebexXmlException(get_class($this) . "::textBox must be of type WebexXmlTrainTextboxInstanceType");
		
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
	 * @param WebexXmlArray<WebexXmlTrainCheckboxGroupInstanceType> $checkBoxGroup
	 */
	public function setCheckBoxGroup(WebexXmlArray $checkBoxGroup)
	{
		if($checkBoxGroup->getType() != 'WebexXmlTrainCheckboxGroupInstanceType')
			throw new WebexXmlException(get_class($this) . "::checkBoxGroup must be of type WebexXmlTrainCheckboxGroupInstanceType");
		
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
	 * @param WebexXmlArray<WebexXmlTrainChoiceGroupInstanceType> $optionButtonGroup
	 */
	public function setOptionButtonGroup(WebexXmlArray $optionButtonGroup)
	{
		if($optionButtonGroup->getType() != 'WebexXmlTrainChoiceGroupInstanceType')
			throw new WebexXmlException(get_class($this) . "::optionButtonGroup must be of type WebexXmlTrainChoiceGroupInstanceType");
		
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
	 * @param WebexXmlArray<WebexXmlTrainChoiceGroupInstanceType> $dropDownGroup
	 */
	public function setDropDownGroup(WebexXmlArray $dropDownGroup)
	{
		if($dropDownGroup->getType() != 'WebexXmlTrainChoiceGroupInstanceType')
			throw new WebexXmlException(get_class($this) . "::dropDownGroup must be of type WebexXmlTrainChoiceGroupInstanceType");
		
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
		
