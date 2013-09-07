<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesProductOrderType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSalesProductOrderByType>
	 */
	protected $orderBy;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlServListOrderADType>
	 */
	protected $orderAD;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'orderBy':
				return 'WebexXmlArray<WebexXmlSalesProductOrderByType>';
	
			case 'orderAD':
				return 'WebexXmlArray<WebexXmlServListOrderADType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'orderBy',
			'orderAD',
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
		return 'productOrderType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlSalesProductOrderByType> $orderBy
	 */
	public function setOrderBy(WebexXmlArray $orderBy)
	{
		if($orderBy->getType() != 'WebexXmlSalesProductOrderByType')
			throw new WebexXmlException(get_class($this) . "::orderBy must be of type WebexXmlSalesProductOrderByType");
		
		$this->orderBy = $orderBy;
	}
	
	/**
	 * @return WebexXmlArray $orderBy
	 */
	public function getOrderBy()
	{
		return $this->orderBy;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlServListOrderADType> $orderAD
	 */
	public function setOrderAD(WebexXmlArray $orderAD)
	{
		if($orderAD->getType() != 'WebexXmlServListOrderADType')
			throw new WebexXmlException(get_class($this) . "::orderAD must be of type WebexXmlServListOrderADType");
		
		$this->orderAD = $orderAD;
	}
	
	/**
	 * @return WebexXmlArray $orderAD
	 */
	public function getOrderAD()
	{
		return $this->orderAD;
	}
	
}

