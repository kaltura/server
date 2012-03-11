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
	protected function getFieldValue() 
	{
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
	}
}