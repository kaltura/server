<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteCodeListType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $index;
	
	/**
	 *
	 * @var string
	 */
	protected $value;
	
	/**
	 *
	 * @var boolean
	 */
	protected $active;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'index':
				return 'integer';
	
			case 'value':
				return 'string';
	
			case 'active':
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
			'index',
			'value',
			'active',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'index',
			'value',
			'active',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'codeListType';
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
	 * @param boolean $active
	 */
	public function setActive($active)
	{
		$this->active = $active;
	}
	
	/**
	 * @return boolean $active
	 */
	public function getActive()
	{
		return $this->active;
	}
	
}
		
