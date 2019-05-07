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
	 * TODO take the default from kConf for on-prem
	 */
	protected $geoCoderType = geoCoderType::KALTURA;
	
	/**
	 * @param int $geoCoderType of enum geoCoderType
	 */
	public function setGeoCoderType($geoCoderType)
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
	public function getFieldValue(kScope $scope)
	{
		$refValues = $this->getStringValues($scope);
		//Get trimmed lower case values of all configured allowed country codes.
		array_walk($refValues, function (&$value) {
			$value = trim(strtolower($value), " \n\r\t");
		});

		kApiCache::addExtraField(array("type" => kApiCache::ECF_COUNTRY,
			kApiCache::ECFD_GEO_CODER_TYPE => $this->getGeoCoderType()),
			kApiCache::COND_COUNTRY_MATCH, $refValues);
		
		$ip = $scope->getIp();
		$ipGeo = kGeoCoderManager::getGeoCoder($this->getGeoCoderType());
		return $ipGeo->getCountry($ip);
	}
	
	/* (non-PHPdoc)
	 * @see kMatchCondition::matches()
	 */
	protected function matches($field, $value)
	{
		return parent::matches(trim(strtolower($field), " \n\r\t"), trim(strtolower($value), " \n\r\t"));
	}

	/* (non-PHPdoc)
	 * @see kMatchCondition::shouldFieldDisableCache()
	 */
	public function shouldFieldDisableCache($scope)
	{
		return false;
	}
}
