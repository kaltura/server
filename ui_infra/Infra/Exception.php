<?php
/**
 * @package UI-infra
 * @subpackage Errors
 */
class Infra_Exception extends Zend_Exception
{
	const ERROR_CODE_MISSING_CLIENT_LIB = 'MISSING_CLIENT_LIB';
	const ERROR_CODE_MISSING_PLUGIN = 'MISSING_PLUGIN';
	const ERROR_CODE_MISSING_PLUGIN_FILE = 'MISSING_PLUGIN_FILE';
	const ERROR_CODE_ACCESS_DENIED = 'ACCESS_DENIED';
	const ERROR_CODE_SESSION_EXPIRED = 'SESSION_EXPIRED';
	
	public function __construct($message, $code, $previous) 
	{
		parent::__construct($message, null, $previous);
		$this->code = $code;
	}
}
