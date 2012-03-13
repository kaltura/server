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
	protected function getFieldValue(accessControlScope $scope = null) 
	{
		if(!$scope)
			$scope = new accessControlScope();
			
		return $scope->getUserAgent();
	}
}