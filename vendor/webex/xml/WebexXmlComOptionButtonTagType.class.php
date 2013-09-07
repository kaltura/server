<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComOptionButtonTagType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $fieldID;
	
	/**
	 *
	 * @var string
	 */
	protected $lable;
	
	/**
	 *
	 * @var integer
	 */
	protected $defaultValue;
	
	/**
	 *
	 * @var integer
	 */
	protected $value;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isRequired;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isDisplay;
	
	/**
	 *
	 * @var WebexXmlArray<string>
	 */
	protected $items;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'fieldID':
				return 'long';
	
			case 'lable':
				return 'string';
	
			case 'defaultValue':
				return 'integer';
	
			case 'value':
				return 'integer';
	
			case 'isRequired':
				return 'boolean';
	
			case 'isDisplay':
				return 'boolean';
	
			case 'items':
				return 'WebexXmlArray<string>';
	
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
			'lable',
			'defaultValue',
			'value',
			'isRequired',
			'isDisplay',
			'items',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'isRequired',
			'isDisplay',
			'items',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'optionButtonTagType';
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
	
	/**
	 * @param string $lable
	 */
	public function setLable($lable)
	{
		$this->lable = $lable;
	}
	
	/**
	 * @return string $lable
	 */
	public function getLable()
	{
		return $this->lable;
	}
	
	/**
	 * @param integer $defaultValue
	 */
	public function setDefaultValue($defaultValue)
	{
		$this->defaultValue = $defaultValue;
	}
	
	/**
	 * @return integer $defaultValue
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}
	
	/**
	 * @param integer $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	/**
	 * @return integer $value
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * @param boolean $isRequired
	 */
	public function setIsRequired($isRequired)
	{
		$this->isRequired = $isRequired;
	}
	
	/**
	 * @return boolean $isRequired
	 */
	public function getIsRequired()
	{
		return $this->isRequired;
	}
	
	/**
	 * @param boolean $isDisplay
	 */
	public function setIsDisplay($isDisplay)
	{
		$this->isDisplay = $isDisplay;
	}
	
	/**
	 * @return boolean $isDisplay
	 */
	public function getIsDisplay()
	{
		return $this->isDisplay;
	}
	
	/**
	 * @param WebexXmlArray<string> $items
	 */
	public function setItems($items)
	{
		if($items->getType() != 'string')
			throw new WebexXmlException(get_class($this) . "::items must be of type string");
		
		$this->items = $items;
	}
	
	/**
	 * @return WebexXmlArray $items
	 */
	public function getItems()
	{
		return $this->items;
	}
	
}
		
