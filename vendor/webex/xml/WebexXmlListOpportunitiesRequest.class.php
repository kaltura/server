<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListOpportunities.class.php');

class WebexXmlListOpportunitiesRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $webExID;
	
	/**
	 *
	 * @var integer
	 */
	protected $intAccountID;
	
	/**
	 *
	 * @var string
	 */
	protected $extAccountID;
	
	/**
	 *
	 * @var integer
	 */
	protected $intOpptyID;
	
	/**
	 *
	 * @var string
	 */
	protected $extOpptyID;
	
	/**
	 *
	 * @var integer
	 */
	protected $extSystemID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExID',
			'intAccountID',
			'extAccountID',
			'intOpptyID',
			'extOpptyID',
			'extSystemID',
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
		return 'sales';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'sales:lstOpportunities';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListOpportunities';
	}
	
	/**
	 * @param string $webExID
	 */
	public function setWebExID($webExID)
	{
		$this->webExID = $webExID;
	}
	
	/**
	 * @param integer $intAccountID
	 */
	public function setIntAccountID($intAccountID)
	{
		$this->intAccountID = $intAccountID;
	}
	
	/**
	 * @param string $extAccountID
	 */
	public function setExtAccountID($extAccountID)
	{
		$this->extAccountID = $extAccountID;
	}
	
	/**
	 * @param integer $intOpptyID
	 */
	public function setIntOpptyID($intOpptyID)
	{
		$this->intOpptyID = $intOpptyID;
	}
	
	/**
	 * @param string $extOpptyID
	 */
	public function setExtOpptyID($extOpptyID)
	{
		$this->extOpptyID = $extOpptyID;
	}
	
	/**
	 * @param integer $extSystemID
	 */
	public function setExtSystemID($extSystemID)
	{
		$this->extSystemID = $extSystemID;
	}
	
}
		
