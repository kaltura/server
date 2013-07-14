<?php

/**
 * Returns the current request IP address context 
 * @package Core
 * @subpackage model.data
 */
class kIpAddressContextField extends kStringField
{
	/* (non-PHPdoc)
	 * @see kIntegerField::getFieldValue()
	 */
	protected function getFieldValue(kScope $scope = null)
	{
		kApiCache::addExtraField(kApiCache::ECF_IP);

		if(!$scope)
			$scope = new kScope();

		return $scope->getIp();
	}

	/* (non-PHPdoc)
	 * @see kStringValue::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		return false;
	}
}