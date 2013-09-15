<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComCheckBoxTagType extends WebexXmlRequestType
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
	 * @var WebexXmlArray<WebexXmlComCheckBoxItemTagType>
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
	
			case 'isRequired':
				return 'boolean';
	
			case 'isDisplay':
				return 'boolean';
	
			case 'items':
				return 'WebexXmlArray<WebexXmlComCheckBoxItemTagType>';
	
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
		return 'checkBoxTagType';
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
	 * @param WebexXmlArray<WebexXmlComCheckBoxItemTagType> $items
	 */
	public function setItems(WebexXmlArray $items)
	{
		if($items->getType() != 'WebexXmlComCheckBoxItemTagType')
			throw new WebexXmlException(get_class($this) . "::items must be of type WebexXmlComCheckBoxItemTagType");
		
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
		
