<?php
require_once ( "getentryAction.class.php");

class getdataentryAction extends getentryAction
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getdataentry",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"playlist_id" => array ("type" => "integer", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"playlist" => array ("type" => "entry", "desc" => ""),
					"embed" => array ("type" => "string", "desc" => "The HTML embed code for this playlist"),
					),
				"errors" => array (
				)
			); 
	}
	
	protected function getObjectPrefix () { return "entry"; }
	
	protected function getCriteria (  ) 
	{ 
		$c = new Criteria();
		$c->addAnd ( entryPeer::TYPE , entry::ENTRY_TYPE_DATA );
		return $c; 
	}
}
?>