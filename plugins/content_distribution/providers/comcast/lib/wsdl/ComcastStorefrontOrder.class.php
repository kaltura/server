<?php


class ComcastStorefrontOrder extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'orderType':
				return 'ComcastStorefrontOrderType';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastStorefrontOrderType
	 **/
	public $orderType;
				
	/**
	 * @var long
	 **/
	public $storefrontPageID;
				
	/**
	 * @var long
	 **/
	public $itemID;
				
	/**
	 * @var long
	 **/
	public $licenseID;
				
	/**
	 * @var boolean
	 **/
	public $copyLicense;
				
	/**
	 * @var boolean
	 **/
	public $prepaid;
				
	/**
	 * @var string
	 **/
	public $subscriptionID;
				
	/**
	 * @var string
	 **/
	public $transactionID;
				
	/**
	 * @var float
	 **/
	public $salesTaxRate;
				
	/**
	 * @var string
	 **/
	public $couponCode;
				
}


