<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kGeoDistanceCondition extends kMatchCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::GEO_DISTANCE);
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
		kApiCache::addExtraField(array("type" => kApiCache::ECF_COORDINATES,
			kApiCache::ECFD_GEO_CODER_TYPE => $this->getGeoCoderType()),
			kApiCache::COND_GEO_DISTANCE, $this->getStringValues($scope));

		$ip = $scope->getIp();
		$ipGeo = kGeoCoderManager::getGeoCoder($this->getGeoCoderType());
		return array($ipGeo->getCoordinates($ip)); // wrap in an array since otherwise the coordinates will be perceived as a list of two values
	}
	
	/* (non-PHPdoc)
	 * @see kMatchCondition::matches()
	 */
	protected function matches($field, $value)
	{
		return kGeoUtils::isInGeoDistance($field, $value);
	}

	/* (non-PHPdoc)
	 * @see kMatchCondition::shouldFieldDisableCache()
	 */
	public function shouldFieldDisableCache($scope)
	{
		return false;
	}
}
