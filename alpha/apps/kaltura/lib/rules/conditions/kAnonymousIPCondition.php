<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAnonymousIPCondition extends kMatchCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::ANONYMOUS_IP);
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
		kApiCache::addExtraField(array("type" => kApiCache::ECF_ANONYMOUS_IP,
			kApiCache::ECFD_GEO_CODER_TYPE => $this->getGeoCoderType()),
			$this->getMatchType() == MatchConditionType::MATCH_ALL ? kApiCache::COND_MATCH_ALL : kApiCache::COND_MATCH, $this->getStringValues($scope));
		
		$ip = $scope->getIp();
		$ipGeo = kGeoCoderManager::getGeoCoder($this->getGeoCoderType());
		return $ipGeo->getAnonymousInfo($ip);
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
