<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSetScheduledTest.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlSetScheduledTestRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var long
	 */
	protected $testID;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $deliveryMethod;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $timeLimit;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sendReport;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $attemptLimit;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionKey',
			'testID',
			'deliveryMethod',
			'timeLimit',
			'sendReport',
			'attemptLimit',
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
			'deliveryMethod',
			'timeLimit',
			'attemptLimit',
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
		return 'trainingsession:setScheduledTest';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSetScheduledTest';
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param long $testID
	 */
	public function setTestID($testID)
	{
		$this->testID = $testID;
	}
	
	/**
	 * @param WebexXml $deliveryMethod
	 */
	public function setDeliveryMethod(WebexXml $deliveryMethod)
	{
		$this->deliveryMethod = $deliveryMethod;
	}
	
	/**
	 * @param WebexXml $timeLimit
	 */
	public function setTimeLimit(WebexXml $timeLimit)
	{
		$this->timeLimit = $timeLimit;
	}
	
	/**
	 * @param boolean $sendReport
	 */
	public function setSendReport($sendReport)
	{
		$this->sendReport = $sendReport;
	}
	
	/**
	 * @param WebexXml $attemptLimit
	 */
	public function setAttemptLimit(WebexXml $attemptLimit)
	{
		$this->attemptLimit = $attemptLimit;
	}
	
}
		
