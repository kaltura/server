<?php
/**
 * @package api
 * @subpackage ps2
 */
class updatedvdentryAction extends updateentryAction
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updateDvdEntry",
				"desc" => "",
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => ""),
						"entry" => array ("type" => "entry", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	
	public function requiredPrivileges () { return "" ; } //"edit:<kshow_id>" ; }
	
	protected function getObjectPrefix () { return "dvdEntry"; } // TODO - fix to be entries
}
?>