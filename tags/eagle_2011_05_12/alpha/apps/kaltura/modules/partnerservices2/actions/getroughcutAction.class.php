<?php
/**
 * @package api
 * @subpackage ps2
 */
require_once 'getentryAction.class.php';

/**
 * @package api
 * @subpackage ps2
 */
class getroughcutAction extends getentryAction
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getRoughCut",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	
	protected function getExtraFields ()
	{
		return array ( "allVersionsFormatted" );
	}
}
?>