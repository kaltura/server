<?php
/**
 * After making sure the ticket is a valid admin ticket - the setrvice is allowed and no other validations should be done
 * 
 * @package api
 * @subpackage ps2
 */
class deleteplaylistAction extends deleteentryAction
{
	public function describe()
	{
		return 
			array (
				"display_name" => "deletePlaylist",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"deleted_playlist" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					 APIErrors::INVALID_ENTRY_ID ,
					 APIErrors::CANNOT_DELETE_ENTRY ,
				)
			); 
	}
	
	protected function getObjectPrefix () { return "playlist"; }
	
	protected function getCriteria (  ) 
	{ 
		$c = new Criteria();
		$c->addAnd ( entryPeer::TYPE , entryType::PLAYLIST );
		return $c; 
	}

}
?>