<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListScheduledTests.class.php');
require_once(__DIR__ . '/WebexXmlSessDateScopeType.class.php');
require_once(__DIR__ . '/WebexXmlTrainTestStatusType.class.php');

class WebexXmlListScheduledTestsRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlSessDateScopeType
	 */
	protected $dateScope;
	
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var WebexXmlTrainTestStatusType
	 */
	protected $status;
	
	/**
	 *
	 * @var string
	 */
	protected $author;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'dateScope',
			'sessionKey',
			'status',
			'author',
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
		return 'trainingsession:lstScheduledTests';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListScheduledTests';
	}
	
	/**
	 * @param WebexXmlSessDateScopeType $dateScope
	 */
	public function setDateScope(WebexXmlSessDateScopeType $dateScope)
	{
		$this->dateScope = $dateScope;
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param WebexXmlTrainTestStatusType $status
	 */
	public function setStatus(WebexXmlTrainTestStatusType $status)
	{
		$this->status = $status;
	}
	
	/**
	 * @param string $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}
	
}
		
