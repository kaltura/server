<?php


class ComcastWMRMSignatureKeys extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var string
	 **/
	public $licenseServerCertificate;
				
	/**
	 * @var string
	 **/
	public $rootCertificate;
				
	/**
	 * @var string
	 **/
	public $privateKey;
				
	/**
	 * @var string
	 **/
	public $signedPublicKey;
				
}


