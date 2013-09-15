<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlCreateAccount.class.php');
require_once(__DIR__ . '/WebexXmlSalesAccountType.class.php');

class WebexXmlCreateAccountRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $webExID;
	
	/**
	 *
	 * @var WebexXmlSalesAccountType
	 */
	protected $account;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExID',
			'account',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'account',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'sales';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'sales:createAccount';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlCreateAccount';
	}
	
	/**
	 * @param string $webExID
	 */
	public function setWebExID($webExID)
	{
		$this->webExID = $webExID;
	}
	
	/**
	 * @param WebexXmlSalesAccountType $account
	 */
	public function setAccount(WebexXmlSalesAccountType $account)
	{
		$this->account = $account;
	}
	
}
		
