<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlSalesProductInstanceType.class.php');

class WebexXmlListProducts extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSalesProductInstanceType>
	 */
	protected $product;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
			case 'product':
				return 'WebexXmlArray<WebexXmlSalesProductInstanceType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
	/**
	 * @return WebexXmlArray $product
	 */
	public function getProduct()
	{
		return $this->product;
	}
	
}

