<?php

/**
 * Returns the current request user agent 
 * @package Core
 * @subpackage model.data
 */
class kUserAgentContextField extends kStringField
{
	/* (non-PHPdoc)
	 * @see kStringField::getFieldValue()
	 */
	protected function getFieldValue(kScope $scope = null) 
	{
		if(!$scope)
			$scope = new kScope();
			
		return $scope->getUserAgent();
	}
}