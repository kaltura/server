<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionCustomFieldsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlTrainTextboxType
	 */
	protected $textBox;
	
	/**
	 *
	 * @var WebexXmlTrainCheckboxGroupType
	 */
	protected $checkBoxGroup;
	
	/**
	 *
	 * @var WebexXmlTrainChoiceGroupType
	 */
	protected $optionButtonGroup;
	
	/**
	 *
	 * @var WebexXmlTrainChoiceGroupType
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
				return 'WebexXmlTrainTextboxType';
	
			case 'checkBoxGroup':
				return 'WebexXmlTrainCheckboxGroupType';
	
			case 'optionButtonGroup':
				return 'WebexXmlTrainChoiceGroupType';
	
			case 'dropDownGroup':
				return 'WebexXmlTrainChoiceGroupType';
	
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
	 * @param WebexXmlTrainTextboxType $textBox
	 */
	public function setTextBox(WebexXmlTrainTextboxType $textBox)
	{
		$this->textBox = $textBox;
	}
	
	/**
	 * @return WebexXmlTrainTextboxType $textBox
	 */
	public function getTextBox()
	{
		return $this->textBox;
	}
	
	/**
	 * @param WebexXmlTrainCheckboxGroupType $checkBoxGroup
	 */
	public function setCheckBoxGroup(WebexXmlTrainCheckboxGroupType $checkBoxGroup)
	{
		$this->checkBoxGroup = $checkBoxGroup;
	}
	
	/**
	 * @return WebexXmlTrainCheckboxGroupType $checkBoxGroup
	 */
	public function getCheckBoxGroup()
	{
		return $this->checkBoxGroup;
	}
	
	/**
	 * @param WebexXmlTrainChoiceGroupType $optionButtonGroup
	 */
	public function setOptionButtonGroup(WebexXmlTrainChoiceGroupType $optionButtonGroup)
	{
		$this->optionButtonGroup = $optionButtonGroup;
	}
	
	/**
	 * @return WebexXmlTrainChoiceGroupType $optionButtonGroup
	 */
	public function getOptionButtonGroup()
	{
		return $this->optionButtonGroup;
	}
	
	/**
	 * @param WebexXmlTrainChoiceGroupType $dropDownGroup
	 */
	public function setDropDownGroup(WebexXmlTrainChoiceGroupType $dropDownGroup)
	{
		$this->dropDownGroup = $dropDownGroup;
	}
	
	/**
	 * @return WebexXmlTrainChoiceGroupType $dropDownGroup
	 */
	public function getDropDownGroup()
	{
		return $this->dropDownGroup;
	}
	
}
		
