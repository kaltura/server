<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteCurrencyType extends WebexXmlRequestType
{
	const _US_DOLLARS = 'US Dollars';
					
	const _AUSTRALIAN_DOLLARS = 'Australian Dollars';
					
	const _CANADIAN_DOLLARS = 'Canadian Dollars';
					
	const _BRITISH_POUNDS = 'British Pounds';
					
	const _EUROS = 'Euros';
					
	const _FRENCH_FRANCS = 'French Francs';
					
	const _DEUTSCHMARKS = 'Deutschmarks';
					
	const _HONG_KONG_DOLLARS = 'Hong Kong Dollars';
					
	const _ITALIAN_LIRA = 'Italian Lira';
					
	const _JAPANESE_YEN = 'Japanese Yen';
					
	const _NEW_ZEALAND_DOLLARS = 'New Zealand Dollars';
					
	const _SWISS_FRANCS = 'Swiss Francs';
					
	const _KOREAN_WON = 'Korean Won';
					
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
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
		return 'currencyType';
	}
	
}
		
