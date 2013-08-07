<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingtypePhoneNumbersType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $primaryTollCallInNumber;
	
	/**
	 *
	 * @var string
	 */
	protected $primaryTollFreeCallInNumber;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'primaryTollCallInNumber':
				return 'string';
	
			case 'primaryTollFreeCallInNumber':
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
			'primaryTollCallInNumber',
			'primaryTollFreeCallInNumber',
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
		return 'phoneNumbersType';
	}
	
	/**
	 * @param string $primaryTollCallInNumber
	 */
	public function setPrimaryTollCallInNumber($primaryTollCallInNumber)
	{
		$this->primaryTollCallInNumber = $primaryTollCallInNumber;
	}
	
	/**
	 * @return string $primaryTollCallInNumber
	 */
	public function getPrimaryTollCallInNumber()
	{
		return $this->primaryTollCallInNumber;
	}
	
	/**
	 * @param string $primaryTollFreeCallInNumber
	 */
	public function setPrimaryTollFreeCallInNumber($primaryTollFreeCallInNumber)
	{
		$this->primaryTollFreeCallInNumber = $primaryTollFreeCallInNumber;
	}
	
	/**
	 * @return string $primaryTollFreeCallInNumber
	 */
	public function getPrimaryTollFreeCallInNumber()
	{
		return $this->primaryTollFreeCallInNumber;
	}
	
}
		
