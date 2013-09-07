<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlSalesAccountInstanceType.class.php');

class WebexXmlListAccounts extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSalesAccountInstanceType>
	 */
	protected $account;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'account':
				return 'WebexXmlArray<WebexXmlSalesAccountInstanceType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $account
	 */
	public function getAccount()
	{
		return $this->account;
	}
	
}

