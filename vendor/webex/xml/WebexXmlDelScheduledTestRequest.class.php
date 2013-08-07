<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlDelScheduledTest.class.php');
require_once(__DIR__ . '/long.class.php');

class WebexXmlDelScheduledTestRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<long>
	 */
	protected $testID;
	
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'testID',
			'sessionKey',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'testID',
			'sessionKey',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'trainingsession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'trainingsession:delScheduledTest';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlDelScheduledTest';
	}
	
	/**
	 * @param WebexXmlArray<long> $testID
	 */
	public function setTestID($testID)
	{
		if($testID->getType() != 'long')
			throw new WebexXmlException(get_class($this) . "::testID must be of type long");
		
		$this->testID = $testID;
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
}
		
