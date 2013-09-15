<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSetOpportunity.class.php');
require_once(__DIR__ . '/WebexXmlSalesOpptyInstanceType.class.php');

class WebexXmlSetOpportunityRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $webExID;
	
	/**
	 *
	 * @var WebexXmlSalesOpptyInstanceType
	 */
	protected $opportunity;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExID',
			'opportunity',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'opportunity',
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
		return 'sales:setOpportunity';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSetOpportunity';
	}
	
	/**
	 * @param string $webExID
	 */
	public function setWebExID($webExID)
	{
		$this->webExID = $webExID;
	}
	
	/**
	 * @param WebexXmlSalesOpptyInstanceType $opportunity
	 */
	public function setOpportunity(WebexXmlSalesOpptyInstanceType $opportunity)
	{
		$this->opportunity = $opportunity;
	}
	
}
		
