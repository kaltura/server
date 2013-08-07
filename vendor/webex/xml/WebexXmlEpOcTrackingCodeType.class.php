<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpOcTrackingCodeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var int
	 */
	protected $index;
	
	/**
	 *
	 * @var string
	 */
	protected $value;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'index':
				return 'int';
	
			case 'value':
				return 'string';
	
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
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'index',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'ocTrackingCodeType';
	}
	
	/**
	 * @param int $index
	 */
	public function setIndex($index)
	{
		$this->index = $index;
	}
	
	/**
	 * @return int $index
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
	
}
		
