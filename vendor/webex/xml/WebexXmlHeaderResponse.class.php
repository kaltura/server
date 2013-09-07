<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlHeaderResponse extends WebexXmlObject
{
	const RESULT_SUCCESS = 'SUCCESS';
	const RESULT_FAILURE = 'FAILURE';
	
	/**
	 * @var string
	 */
	protected $result;
	
	/**
	 * @var string
	 */
	protected $gsbStatus;
	
	/**
	 * @var string
	 */
	protected $reason;
	
	/**
	 * @var int
	 */
	protected $exceptionID;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'result':
				return 'string';
				
			case 'gsbStatus':
				return 'string';
				
			case 'reason':
				return 'string';
				
			case 'exceptionID':
				return 'int';
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $result
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * @return string $gsbStatus
	 */
	public function getGsbStatus()
	{
		return $this->gsbStatus;
	}

	/**
	 * @return string $reason
	 */
	public function getReason()
	{
		return $this->reason;
	}
	
	/**
	 * @return int $exceptionID
	 */
	public function getExceptionID()
	{
		return $this->exceptionID;
	}
}
