<?php
/**
 * @package infra
 * @subpackage Exceptions
 */
class kException extends Exception
{
	public $extra_data = null;
	
	public function __construct($code = null, $message = null)
	{
		parent::__construct($message, $code);
		
		$args = func_get_args();
		$this->extra_data = $args;
	}
}