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
		if(!$scope)
			$scope = new kScope();
			
		return $scope->getIp();
	}
}