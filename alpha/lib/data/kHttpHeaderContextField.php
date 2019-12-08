<?php
/**
 * Returns the current request http headers
 * @package Core
 * @subpackage model.data
 */

class kHttpHeaderContextField extends kStringField
{

	/* (non-PHPdoc)
	 * @see kStringField::getFieldValue()
	 */
	protected function getFieldValue(kScope $scope = null)
	{
		kApiCache::addExtraField(kApiCache::ECFD_IP_HTTP_HEADER);

		if(!$scope)
			$scope = new kScope();

		return null;
	}

	/* (non-PHPdoc)
	 * @see kStringValue::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		return false;
	}

}