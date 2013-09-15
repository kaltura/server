<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComTextBoxTagType extends WebexXmlRequestType
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
	protected $isMultiLines;
	
	/**
	 *
	 * @var integer
	 */
	protected $width;
	
	/**
	 *
	 * @var integer
	 */
	protected $height;
	
	/**
	 *
	 * @var string
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
	
			case 'isMultiLines':
				return 'boolean';
	
			case 'width':
				return 'integer';
	
			case 'height':
				return 'integer';
	
			case 'value':
				return 'string';
	
			case 'isRequired':
				return 'boolean';
	
			case 'isDisplay':
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
			'fieldID',
			'lable',
			'isMultiLines',
			'width',
			'height',
			'value',
			'isRequired',
			'isDisplay',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'isMultiLines',
			'isRequired',
			'isDisplay',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'textBoxTagType';
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
	 * @param boolean $isMultiLines
	 */
	public function setIsMultiLines($isMultiLines)
	{
		$this->isMultiLines = $isMultiLines;
	}
	
	/**
	 * @return boolean $isMultiLines
	 */
	public function getIsMultiLines()
	{
		return $this->isMultiLines;
	}
	
	/**
	 * @param integer $width
	 */
	public function setWidth($width)
	{
		$this->width = $width;
	}
	
	/**
	 * @return integer $width
	 */
	public function getWidth()
	{
		return $this->width;
	}
	
	/**
	 * @param integer $height
	 */
	public function setHeight($height)
	{
		$this->height = $height;
	}
	
	/**
	 * @return integer $height
	 */
	public function getHeight()
	{
		return $this->height;
	}
	
	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	/**
	 * @return string $value
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
	
}
		
