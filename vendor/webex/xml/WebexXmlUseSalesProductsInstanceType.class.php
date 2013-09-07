<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSalesProductsInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXml>
	 */
	protected $product;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'product':
				return 'WebexXmlArray<WebexXml>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'product',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'product',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'salesProductsInstanceType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXml> $product
	 */
	public function setProduct(WebexXmlArray $product)
	{
		if($product->getType() != 'WebexXml')
			throw new WebexXmlException(get_class($this) . "::product must be of type WebexXml");
		
		$this->product = $product;
	}
	
	/**
	 * @return WebexXmlArray $product
	 */
	public function getProduct()
	{
		return $this->product;
	}
	
}
		
