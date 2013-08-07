<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlAuthenticateUser.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlAuthenticateUserRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $samlResponse;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $protocol;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'samlResponse',
			'protocol',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'samlResponse',
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
		return 'use:authenticateUser';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlAuthenticateUser';
	}
	
	/**
	 * @param string $samlResponse
	 */
	public function setSamlResponse($samlResponse)
	{
		$this->samlResponse = $samlResponse;
	}
	
	/**
	 * @param WebexXml $protocol
	 */
	public function setProtocol(WebexXml $protocol)
	{
		$this->protocol = $protocol;
	}
	
}
		
