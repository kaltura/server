<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kCountryCondition extends kMatchCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::COUNTRY);
		parent::__construct($not);
	}
	
	/**
	 * The ip geo coder engine to be used
	 * 
	 * @var int of enum geoCoderType
	 */
	protected $geoCoderType = geoCoderType::KALTURA;
	
	/**
	 * @param int $geoCoderType of enum geoCoderType
	 */
	public function setGeoCoderType(array $geoCoderType)
	{
		$this->geoCoderType = $geoCoderType;
	}
	
	/**
	 * @return array
	 */
	function getGeoCoderType()
	{
		return $this->geoCoderType;
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::getFieldValue()
	 */
	public function getFieldValue(accessControl $accessControl)
	{
		$scope = $accessControl->getScope();
		$ip = $scope->getIp();
		$ipGeo = kGeoCoderManager::getGeoCoder($this->getGeoCoderType());
		$country = $ipGeo->getCountry($ip);
		return trim(strtolower($country), " \n\r\t");
	}
}
