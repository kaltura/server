<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServSubErrorType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $exceptionID;
	
	/**
	 *
	 * @var string
	 */
	protected $reason;
	
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
			case 'exceptionID':
				return 'string';
	
			case 'reason':
				return 'string';
	
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
			'exceptionID',
			'reason',
			'value',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'exceptionID',
			'reason',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'subErrorType';
	}
	
	/**
	 * @param string $exceptionID
	 */
	public function setExceptionID($exceptionID)
	{
		$this->exceptionID = $exceptionID;
	}
	
	/**
	 * @return string $exceptionID
	 */
	public function getExceptionID()
	{
		return $this->exceptionID;
	}
	
	/**
	 * @param string $reason
	 */
	public function setReason($reason)
	{
		$this->reason = $reason;
	}
	
	/**
	 * @return string $reason
	 */
	public function getReason()
	{
		return $this->reason;
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
		
