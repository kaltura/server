<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlSalesSalesSessionSummaryInstanceType.class.php');

class WebexXmlListSummarySalesSession extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSalesSalesSessionSummaryInstanceType>
	 */
	protected $salesSession;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
			case 'salesSession':
				return 'WebexXmlArray<WebexXmlSalesSalesSessionSummaryInstanceType>';
	
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
	 * @return WebexXmlArray $salesSession
	 */
	public function getSalesSession()
	{
		return $this->salesSession;
	}
	
}

