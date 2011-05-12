<?php


class ComcastPrice extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfPriceField';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfPriceField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $couponCode;
				
	/**
	 * @var float
	 **/
	public $initialPrice;
				
	/**
	 * @var long
	 **/
	public $licenseID;
				
	/**
	 * @var long
	 **/
	public $periodsPerRenewalCharge;
				
	/**
	 * @var boolean
	 **/
	public $preventDirectUse;
				
	/**
	 * @var float
	 **/
	public $pricePerLicense;
				
	/**
	 * @var long
	 **/
	public $renewalChargesAtSpecialPrice;
				
	/**
	 * @var float
	 **/
	public $renewalPrice;
				
	/**
	 * @var float
	 **/
	public $specialRenewalPrice;
				
}


