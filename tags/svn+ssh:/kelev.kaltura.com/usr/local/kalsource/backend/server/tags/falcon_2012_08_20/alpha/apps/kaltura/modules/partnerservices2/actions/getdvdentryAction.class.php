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
class getdvdentryAction extends getentryAction
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getDvdEntry",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"dvdEntry_id" => array ("type" => "integer", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"dvdEntry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	
	protected function getObjectPrefix () { return "dvdEntry"; }
}
?>