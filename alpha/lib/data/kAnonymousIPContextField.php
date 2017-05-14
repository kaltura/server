<?php

/**
 * Return the current request coodinates context as calculated based on the IP address 
 * @package Core
 * @subpackage model.data
 */
class kAnonymousIPContextField extends kStringField
{
	/**
	 * The ip geo coder engine to be used
	 * 
	 * @var int of enum geoCoderType
	 */
	protected $geoCoderType = geoCoderType::KALTURA;
	
	/* (non-PHPdoc)
	 * @see kIntegerField::getFieldValue()
	 */
	protected function getFieldValue(kScope $scope = null)
	{
		kApiCache::addExtraField(kApiCache::ECF_ANONYMOUS_IP);

		if(!$scope)
			$scope = new kScope();
			
		$ip = $scope->getIp();
		$ipGeo = kGeoCoderManager::getGeoCoder($this->getGeoCoderType());
		$anonymousIPInfo = $ipGeo->getAnonymousInfo($ip);
		return $anonymousIPInfo;
	}
	
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
	 * @see kStringValue::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		return false;
	}
}