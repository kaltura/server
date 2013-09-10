<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComPhoneLabelNumType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $label;
	
	/**
	 *
	 * @var string
	 */
	protected $phoneNumber;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'label':
				return 'string';
	
			case 'phoneNumber':
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
			'label',
			'phoneNumber',
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
		return 'phoneLabelNumType';
	}
	
	/**
	 * @param string $label
	 */
	public function setLabel($label)
	{
		$this->label = $label;
	}
	
	/**
	 * @return string $label
	 */
	public function getLabel()
	{
		return $this->label;
	}
	
	/**
	 * @param string $phoneNumber
	 */
	public function setPhoneNumber($phoneNumber)
	{
		$this->phoneNumber = $phoneNumber;
	}
	
	/**
	 * @return string $phoneNumber
	 */
	public function getPhoneNumber()
	{
		return $this->phoneNumber;
	}
	
}
		
