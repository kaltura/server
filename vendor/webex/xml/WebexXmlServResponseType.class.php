<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServResponseType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlServResultTypeType
	 */
	protected $result;
	
	/**
	 *
	 * @var string
	 */
	protected $reason;
	
	/**
	 *
	 * @var WebexXmlServGsbStatusType
	 */
	protected $gsbStatus;
	
	/**
	 *
	 * @var string
	 */
	protected $exceptionID;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlServSubErrorType>
	 */
	protected $subErrors;
	
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
			case 'result':
				return 'WebexXmlServResultTypeType';
	
			case 'reason':
				return 'string';
	
			case 'gsbStatus':
				return 'WebexXmlServGsbStatusType';
	
			case 'exceptionID':
				return 'string';
	
			case 'subErrors':
				return 'WebexXmlArray<WebexXmlServSubErrorType>';
	
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
			'result',
			'reason',
			'gsbStatus',
			'exceptionID',
			'subErrors',
			'value',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'result',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'responseType';
	}
	
	/**
	 * @param WebexXmlServResultTypeType $result
	 */
	public function setResult(WebexXmlServResultTypeType $result)
	{
		$this->result = $result;
	}
	
	/**
	 * @return WebexXmlServResultTypeType $result
	 */
	public function getResult()
	{
		return $this->result;
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
	 * @param WebexXmlServGsbStatusType $gsbStatus
	 */
	public function setGsbStatus(WebexXmlServGsbStatusType $gsbStatus)
	{
		$this->gsbStatus = $gsbStatus;
	}
	
	/**
	 * @return WebexXmlServGsbStatusType $gsbStatus
	 */
	public function getGsbStatus()
	{
		return $this->gsbStatus;
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
	 * @param WebexXmlArray<WebexXmlServSubErrorType> $subErrors
	 */
	public function setSubErrors(WebexXmlArray $subErrors)
	{
		if($subErrors->getType() != 'WebexXmlServSubErrorType')
			throw new WebexXmlException(get_class($this) . "::subErrors must be of type WebexXmlServSubErrorType");
		
		$this->subErrors = $subErrors;
	}
	
	/**
	 * @return WebexXmlArray $subErrors
	 */
	public function getSubErrors()
	{
		return $this->subErrors;
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
		
