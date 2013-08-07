<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventTextboxType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $label;
	
	/**
	 *
	 * @var WebexXmlEventTextboxTypeType
	 */
	protected $type;
	
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
	
			case 'type':
				return 'WebexXmlEventTextboxTypeType';
	
			case 'width':
				return 'integer';
	
			case 'height':
				return 'integer';
	
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
			'type',
			'width',
			'height',
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
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'textboxType';
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
	 * @param WebexXmlEventTextboxTypeType $type
	 */
	public function setType(WebexXmlEventTextboxTypeType $type)
	{
		$this->type = $type;
	}
	
	/**
	 * @return WebexXmlEventTextboxTypeType $type
	 */
	public function getType()
	{
		return $this->type;
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
		
