<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListProducts.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlSalesProductOrderType.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/integer.class.php');

class WebexXmlListProductsRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlSalesProductOrderType
	 */
	protected $order;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $name;
	
	/**
	 *
	 * @var WebexXmlArray<integer>
	 */
	protected $prodID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'listControl',
			'order',
			'name',
			'prodID',
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
		return 'sales:lstProducts';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListProducts';
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlSalesProductOrderType $order
	 */
	public function setOrder(WebexXmlSalesProductOrderType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param WebexXml $name
	 */
	public function setName(WebexXml $name)
	{
		$this->name = $name;
	}
	
	/**
	 * @param WebexXmlArray<integer> $prodID
	 */
	public function setProdID($prodID)
	{
		if($prodID->getType() != 'integer')
			throw new WebexXmlException(get_class($this) . "::prodID must be of type integer");
		
		$this->prodID = $prodID;
	}
	
}
		
