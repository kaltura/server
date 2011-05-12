<?php
/**
 * @package infra
 * @subpackage Exceptions
 */
class kException extends Exception
{
	public $kaltura_code = null;
	public $extra_data = null;
	
	public function __construct()
	{
		parent::__construct();
		
		$args = func_get_args();
		
		if ( $args && count ( $args) > 0 )
		{
			$this->kaltura_code = $args[0];
			$this->extra_data = $args;
		}
	}
}