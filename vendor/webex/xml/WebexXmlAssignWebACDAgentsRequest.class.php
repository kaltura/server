<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlAssignWebACDAgents.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlAssignWebACDAgentsRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $manager;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXml>
	 */
	protected $webACDUser;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'manager',
			'webACDUser',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'manager',
			'webACDUser',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'use';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'use:AssignWebACDAgents';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlAssignWebACDAgents';
	}
	
	/**
	 * @param string $manager
	 */
	public function setManager($manager)
	{
		$this->manager = $manager;
	}
	
	/**
	 * @param WebexXmlArray<WebexXml> $webACDUser
	 */
	public function setWebACDUser(WebexXmlArray $webACDUser)
	{
		if($webACDUser->getType() != 'WebexXml')
			throw new WebexXmlException(get_class($this) . "::webACDUser must be of type WebexXml");
		
		$this->webACDUser = $webACDUser;
	}
	
}
		
