<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistorySalesSessionHistoryInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $account;
	
	/**
	 *
	 * @var string
	 */
	protected $opportunity;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'account':
				return 'string';
	
			case 'opportunity':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'account',
			'opportunity',
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
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'salesSessionHistoryInstanceType';
	}
	
	/**
	 * @param string $account
	 */
	public function setAccount($account)
	{
		$this->account = $account;
	}
	
	/**
	 * @return string $account
	 */
	public function getAccount()
	{
		return $this->account;
	}
	
	/**
	 * @param string $opportunity
	 */
	public function setOpportunity($opportunity)
	{
		$this->opportunity = $opportunity;
	}
	
	/**
	 * @return string $opportunity
	 */
	public function getOpportunity()
	{
		return $this->opportunity;
	}
	
}
		
