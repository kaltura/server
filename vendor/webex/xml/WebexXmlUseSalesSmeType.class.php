<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSalesSmeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $description;
	
	/**
	 *
	 * @var WebexXmlUseSalesProductsType
	 */
	protected $products;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'description':
				return 'WebexXml';
	
			case 'products':
				return 'WebexXmlUseSalesProductsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'description',
			'products',
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
		return 'salesSmeType';
	}
	
	/**
	 * @param WebexXml $description
	 */
	public function setDescription(WebexXml $description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return WebexXml $description
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * @param WebexXmlUseSalesProductsType $products
	 */
	public function setProducts(WebexXmlUseSalesProductsType $products)
	{
		$this->products = $products;
	}
	
	/**
	 * @return WebexXmlUseSalesProductsType $products
	 */
	public function getProducts()
	{
		return $this->products;
	}
	
}
		
