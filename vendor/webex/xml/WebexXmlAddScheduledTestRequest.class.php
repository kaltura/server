<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlAddScheduledTest.class.php');
require_once(__DIR__ . '/long.class.php');

class WebexXmlAddScheduledTestRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var WebexXmlArray<long>
	 */
	protected $testID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionKey',
			'testID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'sessionKey',
			'testID',
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
		return 'trainingsession:addScheduledTest';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlAddScheduledTest';
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
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
	
}
		
