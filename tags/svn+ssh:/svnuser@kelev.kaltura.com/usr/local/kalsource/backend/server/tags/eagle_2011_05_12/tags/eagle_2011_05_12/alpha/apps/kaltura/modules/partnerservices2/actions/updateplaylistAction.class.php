<?php
/**
 * @package api
 * @subpackage ps2
 */
require_once 'updateentryAction.class.php';

/**
 * @package api
 * @subpackage ps2
 */
class updateplaylistAction extends updateentryAction
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updatePlaylist",
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
					"playlist" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	
	public function requiredPrivileges () { return "" ; } //"edit:<kshow_id>" ; }
	
	protected function getObjectPrefix () { return "playlist"; } // TODO - fix to be entries

	protected function validateEntry ( $entry ) 
	{
		myPlaylistUtils::validatePlaylist( $entry );
		
		// this is a small hack - to use this hook to update the count, countDate & the lengthInMsecs for the playlist 
		// only if set "update_stats"
		$update_stats = $this->getP ( "update_stats" );
		if ( $update_stats == "false" || $update_stats === "0" ) $update_stats = false;
		if ( $update_stats )
			myPlaylistUtils::updatePlaylistStatistics( $entry->getPartnerId() , $entry );
			
		$entry->setDisplayInSearch ( 2 ); // make all the playlist entries PUBLIC !
	}
}
?>