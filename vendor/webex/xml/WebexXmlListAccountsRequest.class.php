<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListAccounts.class.php');

class WebexXmlListAccountsRequest extends WebexXmlRequestBodyContent
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
	protected $extSystemID;
	
	/**
	 *
	 * @var boolean
	 */
	protected $returnOppty;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExID',
			'intAccountID',
			'extAccountID',
			'extSystemID',
			'returnOppty',
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
		return 'sales:lstAccounts';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListAccounts';
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
	 * @param integer $extSystemID
	 */
	public function setExtSystemID($extSystemID)
	{
		$this->extSystemID = $extSystemID;
	}
	
	/**
	 * @param boolean $returnOppty
	 */
	public function setReturnOppty($returnOppty)
	{
		$this->returnOppty = $returnOppty;
	}
	
}
		
