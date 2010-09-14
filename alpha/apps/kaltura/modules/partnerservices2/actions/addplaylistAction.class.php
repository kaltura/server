<?php
require_once ( "addentrybaseAction.class.php");

class addplaylistAction extends addentrybaseAction
{
	public function describe()
	{
		return 
			array (
				"display_name" => "addPlaylist",
				"desc" => "Create a new entry of type playlist" ,
				"in" => array (
					"mandatory" => array ( 
						"playlist" => array ("type" => "entry", "desc" => "Entry of type ENTRY_TYPE_PLAYLIST"),
						),
					"optional" => array (
						)
					),
				"out" => array (
					"playlist" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::NO_FIELDS_SET_FOR_GENERIC_ENTRY ,
					APIErrors::INVALID_PLAYLIST_TYPE ,
				)
			); 
	}
	
 
	protected function getDetailed()
	{
		return $this->getP ( "detailed" , true );
	}
		
	protected function getObjectPrefix () {  return "playlist"; }

	protected function setTypeAndMediaType ( $entry ) 
	{
		$entry->setType ( entry::ENTRY_TYPE_PLAYLIST );
		// media_type can be either ENTRY_MEDIA_TYPE_XML or ??
	}
	
	protected function validateEntry ( $entry ) 
	{
		myPlaylistUtils::validatePlaylist( $entry );
		
		// this is a small hack - to use this hook to update the count, countDate & the lengthInMsecs for the playlist 
		// only if set "update_stats"
		if ( $this->getP ( "update_stats" ) )
			myPlaylistUtils::updatePlaylistStatistics( $entry->getPartnerId() , $entry );
		$entry->setDisplayInSearch ( 2 ); // make all the playlist entries PUBLIC !
		
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
/*
		// make the playlist_mediaType a mandatory field - 
		$media_type = $this->getPm ( $this->getObjectPrefix() .  "_mediaType" );
		if ( ! in_array ( $media_type , array ( entry::ENTRY_MEDIA_TYPE_GENERIC_1 , entry::ENTRY_MEDIA_TYPE_XML , entry::ENTRY_MEDIA_TYPE_TEXT)  ) )
		{
			$this->addException( APIErrors::INVALID_PLAYLIST_TYPE );
		}
*/
		// validate the xml of the dynamic playlist and update the update the entry_group table for a static playlist   
		$res = parent::executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser );
		
		
	}
}
?>