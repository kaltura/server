<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComCheckBoxItemTagType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $itemName;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isSelected;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'itemName':
				return 'string';
	
			case 'isSelected':
				return 'boolean';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'itemName',
			'isSelected',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'itemName',
			'isSelected',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'checkBoxItemTagType';
	}
	
	/**
	 * @param string $itemName
	 */
	public function setItemName($itemName)
	{
		$this->itemName = $itemName;
	}
	
	/**
	 * @return string $itemName
	 */
	public function getItemName()
	{
		return $this->itemName;
	}
	
	/**
	 * @param boolean $isSelected
	 */
	public function setIsSelected($isSelected)
	{
		$this->isSelected = $isSelected;
	}
	
	/**
	 * @return boolean $isSelected
	 */
	public function getIsSelected()
	{
		return $this->isSelected;
	}
	
}
		
