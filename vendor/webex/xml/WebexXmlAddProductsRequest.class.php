<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlAddProducts.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlSalesProductType.class.php');

class WebexXmlAddProductsRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSalesProductType>
	 */
	protected $product;
	
	
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
		return 'sales:addProducts';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlAddProducts';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlSalesProductType> $product
	 */
	public function setProduct(WebexXmlArray $product)
	{
		if($product->getType() != 'WebexXmlSalesProductType')
			throw new WebexXmlException(get_class($this) . "::product must be of type WebexXmlSalesProductType");
		
		$this->product = $product;
	}
	
}
		
