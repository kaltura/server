<?php
/**
 * @package api
 * @subpackage ps2
 */
class appendentrytoroughcutAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "appendEntryToRoughcut",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => ""),
						"kshow_id" => array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						"show_entry_id" => array ("type" => "string", "desc" => ""),
						)
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => ""),
					"kshow" => array ("type" => "kshow", "desc" => ""),
					"metadata" => array ("type" => "xml", "desc" => "xml after updating")
					),
				"errors" => array (
					APIErrors::INVALID_KSHOW_ID , 
					APIErrors::INVALID_ENTRY_ID ,
				)
			); 		
	}
	
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_KUSER_ID_ONLY;	}
	
	protected function addUserOnDemand ( )  { 		return self::CREATE_USER_FORCE; }
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$entry_id = $this->getP ( "entry_id" );
		$kshow_id =  $this->getP ( "kshow_id" );
		$show_entry_id = $this->getP ( "show_entry_id" );
		
		// Make sure the request is for a ready roughcut
		$c = entryPeer::getCriteriaFilter()->getFilter();
		$c->addAnd ( entryPeer::STATUS, entryStatus::READY , Criteria::EQUAL);
				
		list ( $kshow , $show_entry , $error , $error_obj ) = myKshowUtils::getKshowAndEntry( $kshow_id  , $show_entry_id );

		if ( $error_obj )
		{
			$this->addError ( $error_obj );
			return ;
		}

		$entry = entryPeer::retrieveByPK( $entry_id );
		if ( ! $entry )
		{
			$this->addError ( APIErrors::INVALID_ENTRY_ID, "entry" , $entry_id );
			return;
		}

		$metadata = $kshow->getMetadata();

		$relevant_kshow_version = 1 + $kshow->getVersion(); // the next metadata will be the first relevant version for this new entry
		$version_info = array();
		$version_info["KuserId"] = $puser_kuser->getKuserId();
		$version_info["PuserId"] = $puser_id;
		$version_info["ScreenName"] = $puser_kuser->getPuserName();
		
		$new_metadata = myMetadataUtils::addEntryToMetadata ( $metadata , $entry ,$relevant_kshow_version, $version_info );
		$entry_modified = true;
		if ( $new_metadata )
		{
		    // TODO - add thumbnail only for entries that are worthy - check they are not moderated !
		    $thumb_modified = myKshowUtils::updateThumbnail ( $kshow , $entry , false );
		
		    if ( $thumb_modified )
		    {
		        $new_metadata = myMetadataUtils::updateThumbUrlFromMetadata ( $new_metadata , $entry->getThumbnailUrl() );
		    }
		    // it is very important to increment the version count because even if the entry is deferred
		    // it will be added on the next version
		
		 if ( ! $kshow->getHasRoughcut (  ) )
		 {
		 	// make sure the kshow now does have a roughcut
		 	$kshow->setHasRoughcut ( true );	
		 	$kshow->save();
		 }
		
		    $kshow->setMetadata ( $new_metadata, true ) ;
		}
		
		$this->addMsg ( "entry" , objectWrapperBase::getWrapperClass( $entry ,  objectWrapperBase::DETAIL_LEVEL_REGULAR ) );
		$this->addMsg ( "kshow" , objectWrapperBase::getWrapperClass( $kshow ,  objectWrapperBase::DETAIL_LEVEL_REGULAR ) );
		$this->addMsg ( "metadata" , $new_metadata );
		
	}
}
?>