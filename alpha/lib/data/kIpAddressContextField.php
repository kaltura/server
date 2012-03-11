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
	protected function getFieldValue()
	{
		return requestUtils::getRemoteAddress();
	}	
}