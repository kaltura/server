<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class getallentriesAction extends defPartnerservices2Action
{
	const LIST_TYPE_KSHOW = 1 ;
	const LIST_TYPE_KUSER = 2 ;
	const LIST_TYPE_ROUGHCUT = 4 ;
	const LIST_TYPE_EPISODE = 8 ;
	const LIST_TYPE_ALL = 15;

	public function describe()
	{
		return 
			array (
				"display_name" => "getAllEntries",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => ""),
						"kshow_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						"list_type" => array ("type" => "integer", "desc" => ""), // TODO: describe enum
						"version" => array ("type" => "integer", "desc" => ""),
						"disable_roughcut_entry_data" => array ("type" => "boolean", "desc" => "indicaes the roughcut_entry_data is not required in the response"),
						)
					),
				"out" => array (
					"show" => array ("type" => "*entry", "desc" => ""),
					"roughcut_entry_data" => array ("type" => "array", "desc" => ""),
					"user" => array ("type" => "*entry", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	
	protected function ticketType ()	{		return self::REQUIED_TICKET_REGULAR;	}

	protected function needKuserFromPuser ( )	
	{	
			return self::KUSER_DATA_NO_KUSER;
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$entry_id = $this->getP ( "entry_id" );
		
		// if the entry_type was sent by the client - make sure it's of type  ENTRY_TYPE_SHOW.
		// this is to prevent this service as part of a bad multirequest
		$entry_type = $this->getP ( "entry_type" , null );
		if ( ! empty ( $entry_type ) && $entry_type != entryType::MIX )
		{
			$this->addDebug ( "entry" , "not of type " . entryType::MIX );
			return; 
		}
		
		$kshow_id =  $this->getP ( "kshow_id" );
		list ( $kshow , $entry , $error , $error_obj ) = myKshowUtils::getKshowAndEntry( $kshow_id  , $entry_id );

		if ( $error_obj )
		{
			$this->addError ( $error_obj );
			return ;
		}

		KalturaLog::log ( __METHOD__ . " kshow_id [$kshow_id] entry_id [$entry_id]" );


		$list_type = $this->getP ( "list_type" , self::LIST_TYPE_ROUGHCUT );
		$version = $this->getP ( "version" , null  );
		if ((int)$version == -1)
			$version = null; // will retrieve the latest
		$disable_roughcut_entry_data = $this->getP ( "disable_roughcut_entry_data" , false  );
		$disable_user_data = $this->getP ( "disable_user_data" , false  ); // will allow to optimize the cals and NOT join with the kuser table
		
		$merge_entry_lists = false;
		if ( $this->getPartner() )
		{
			$merge_entry_lists = $this->getPartner()->getMergeEntryLists();
		}
		
		$kshow_entry_list = array();
		$kuser_entry_list = array();

		$aggrigate_id_list = array();
$this->benchmarkStart( "list_type_kshow" );		
		if ( $list_type & self::LIST_TYPE_KSHOW )
		{
			$c = new Criteria();
			$c->addAnd ( entryPeer::TYPE , entryType::MEDIA_CLIP );
//			$c->addAnd ( entryPeer::MEDIA_TYPE , entry::ENTRY_MEDIA_TYPE_SHOW , Criteria::NOT_EQUAL );
			$c->addAnd ( entryPeer::KSHOW_ID , $kshow_id );
			$this->addIgnoreIdList ($c , $aggrigate_id_list);
			
//			$this->addOffsetAndLimit ( $c ); // fetch as many as the kshow has
			if ( $disable_user_data )
				$kshow_entry_list = entryPeer::doSelect( $c );
			else
				$kshow_entry_list = entryPeer::doSelectJoinkuser( $c );
				
			$this->updateAggrigatedIdList( $aggrigate_id_list , $kshow_entry_list );
		}
		
$this->benchmarkEnd ( "list_type_kshow" );
$this->benchmarkStart( "list_type_kuser" );
		if ( $list_type & self::LIST_TYPE_KUSER )
		{
			// try to get puserKuser - PS2
			$puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid ( $partner_id , null /*$subp_id*/,  $puser_id , false );
			// try to get kuser by partnerId & puserId - PS3 - backward compatibility
			$apiv3Kuser = kuserPeer::getKuserByPartnerAndUid($partner_id, $puser_id, true);
			
			if ( ($puser_kuser && $puser_kuser->getKuserId()) || $apiv3Kuser )
			{
				$c = new Criteria();
				$c->addAnd ( entryPeer::TYPE , entryType::MEDIA_CLIP );
//				$c->addAnd ( entryPeer::MEDIA_TYPE , entry::ENTRY_MEDIA_TYPE_SHOW , Criteria::NOT_EQUAL );
				$kuserIds = array();
				if($puser_kuser && $puser_kuser->getKuserId())
				{
					$kuserIds[] = $puser_kuser->getKuserId();
				}
				if($apiv3Kuser)
				{
					if(!$puser_kuser || ($puser_kuser->getKuserId() != $apiv3Kuser->getId()))
					{
						$kuserIds[] = $apiv3Kuser->getId();
					}
				}
				if(count($kuserIds) > 1)
				{
					$c->addAnd ( entryPeer::KUSER_ID , $kuserIds, Criteria::IN );
				}
				else
				{
					$c->addAnd ( entryPeer::KUSER_ID , $kuserIds[0] );
				}
				if ( $merge_entry_lists )
				{
					// if will join lists - no need to fetch entries twice
					$this->addIgnoreIdList ($c , $aggrigate_id_list);
				}
				$this->addOffsetAndLimit ( $c ); // limit the number of the user's clips
				if ( $disable_user_data )
					$kuser_entry_list = entryPeer::doSelect( $c );
				else
					$kuser_entry_list = entryPeer::doSelectJoinkuser( $c );
					
				// Since we are using 2 potential kusers, we might not have the obvious kuser from $puser_kuser
				$strEntries = "";
				if($puser_kuser)
				{	
					$kuser = KuserPeer::retrieveByPk($puser_kuser->getKuserId());
					if ($kuser)
					{
						$strEntriesTemp = @unserialize($kuser->getPartnerData());
						if ($strEntriesTemp)
							$strEntries .= $strEntriesTemp;
					}
				}
				if ($apiv3Kuser)
				{
					$strEntriesTemp = @unserialize($apiv3Kuser->getPartnerData());
					if ($strEntriesTemp)
							$strEntries .= $strEntriesTemp;
				}
				
				if ($strEntries)
				{
					$entries = explode(',', $strEntries);
					$fixed_entry_list = array();
					foreach ( $entries as $entryId ) {
					  $fixed_entry_list[] = trim($entryId);
					}
					$c = new Criteria();
					$c->addAnd ( entryPeer::TYPE , entryType::MEDIA_CLIP );
					$c->addAnd ( entryPeer::ID , $fixed_entry_list, Criteria::IN );
					if ( $merge_entry_lists )
					{
						// if will join lists - no need to fetch entries twice
						$this->addIgnoreIdList ($c , $aggrigate_id_list);
					}
					if ( $disable_user_data )
						$extra_user_entries = entryPeer::doSelect( $c );
					else
						$extra_user_entries = entryPeer::doSelectJoinkuser( $c );
										
					if (count($extra_user_entries))
					{
						$kuser_entry_list = array_merge($extra_user_entries, $kuser_entry_list);
					}
				}
			}
			else
			{
				$kuser_entry_list = array();
			}	
			
			if ( $merge_entry_lists )
			{
				$kshow_entry_list = kArray::append  ( $kshow_entry_list , $kuser_entry_list );
				$kuser_entry_list = null;
			}
		}
$this->benchmarkEnd( "list_type_kuser" );
$this->benchmarkStart( "list_type_episode" );
		if ( $list_type & self::LIST_TYPE_EPISODE )
		{
			if ( $kshow->getEpisodeId() )
			{
				// episode_id will point to the "parent" kshow
				// fetch the entries of the parent kshow
				$c = new Criteria();
				$c->addAnd ( entryPeer::TYPE , entryType::MEDIA_CLIP );
//				$c->addAnd ( entryPeer::MEDIA_TYPE , entry::ENTRY_MEDIA_TYPE_SHOW , Criteria::NOT_EQUAL );
				$c->addAnd ( entryPeer::KSHOW_ID , $kshow->getEpisodeId() );
				$this->addIgnoreIdList ($c , $aggrigate_id_list);
//				$this->addOffsetAndLimit ( $c ); // limit the number of the inherited entries from the episode
				if ( $disable_user_data )
					$parent_kshow_entries = entryPeer::doSelect( $c );
				else
					$parent_kshow_entries = entryPeer::doSelectJoinkuser( $c );
				
				if ( count ( $parent_kshow_entries) )
				{
					$kshow_entry_list = kArray::append  ( $kshow_entry_list , $parent_kshow_entries );
				}
			}
		}
$this->benchmarkEnd( "list_type_episode" );
		// fetch all entries that were used in the roughcut - those of other kusers
		// - appeared under kuser_entry_list when someone else logged in
$this->benchmarkStart( "list_type_roughcut" );
		$entry_data_from_roughcut_map = array(); // will hold an associative array where the id is the key
		if ( $list_type & self::LIST_TYPE_ROUGHCUT )
		{
			if ( $entry->getType() == entryType::MIX ) //&& $kshow->getHasRoughcut() )
			{
				$sync_key = $entry->getSyncKey ( entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA , $version );
				$roughcut_file_name = kFileSyncUtils::getReadyLocalFilePathForKey ( $sync_key );

				$entry_data_from_roughcut = myFlvStreamer::getAllAssetsData ( $roughcut_file_name );

				$final_id_list = array();
				foreach ( $entry_data_from_roughcut as $data )
				{
					$id = $data["id"]; // first element is the id
					$entry_data_from_roughcut_map[] = $data;
					$found = false;
					foreach ( $kshow_entry_list as $entry )
					{
						// see we are not fetching duplicate entries
						if ( $entry->getId() == $id )
						{
							$found = true;
							break;
						}
					}
					if ( !$found )	$final_id_list[] = $id;
				}

				if ( count ( $final_id_list) > 0 ) // this is so we won't go to the DB for nothing - we'll receive an empty list anyway
				{
					// allow deleted entries when searching for entries on the roughcut 
					// don't forget to return the status at the end of the process
					entryPeer::allowDeletedInCriteriaFilter();
					
					$c = new Criteria();
					$c->addAnd ( entryPeer::ID , $final_id_list , Criteria::IN );
                	$c->addAnd ( entryPeer::TYPE , entryType::MEDIA_CLIP );					
					$this->addIgnoreIdList ($c , $aggrigate_id_list);
	//				$this->addOffsetAndLimit ( $c );
					
					if ( $disable_user_data )
						$extra_entries = entryPeer::doSelect( $c );
					else
						$extra_entries = entryPeer::doSelectJoinkuser( $c );
		
					// return the status to the criteriaFilter
					entryPeer::blockDeletedInCriteriaFilter();
						
					// merge the 2 lists into 1:
					$kshow_entry_list = kArray::append  ( $kshow_entry_list , $extra_entries );
				}
			}
		}
$this->benchmarkEnd( "list_type_roughcut" );
$this->benchmarkStart( "create_wrapper" );
		$entry_wrapper =  objectWrapperBase::getWrapperClass( $kshow_entry_list , objectWrapperBase::DETAIL_LEVEL_REGULAR , -3 ,0 ,array ( "contributorScreenName" ) );
		//$entry_wrapper->addFields ( array ( "kuser.screenName" ) );
		$this->addMsg ( "show" , $entry_wrapper );
		// if ! $disable_roughcut_entry_data - add the roughcut_entry_data
		if ( ! $disable_roughcut_entry_data )
			$this->addMsg ( "roughcut_entry_data" , $entry_data_from_roughcut_map );
		if ( count ( $kuser_entry_list ) > 0 ) 
		{
			$this->addMsg ( "user" ,  objectWrapperBase::getWrapperClass( $kuser_entry_list , objectWrapperBase::DETAIL_LEVEL_REGULAR ) );
		}
		else
		{
			$this->addMsg ( "user" ,  null );
		}
		
$this->benchmarkEnd( "create_wrapper" );		
	}
	
	private function addOffsetAndLimit ( Criteria $c )
	{
		$size = $this->getP ( "page_size" , 40 );
		if ( $size > 100 ) $size = 100;
		$page = $this->getP ( "page" , 1 );
				
		$offset = ($page-1)* $size;
		if ( $offset > 0 )	$c->setOffset( $offset );		
		
		$c->setLimit( $size);
	}
	
	private function addIgnoreIdList ( Criteria $c , $list )
	{
		if ( $list && count ( $list ) > 0 )
		{
			$c->addAnd ( entryPeer::ID , $list , Criteria::NOT_IN );
		}
	}
	
	private function updateAggrigatedIdList( &$aggrigate_id_list , $entry_list )
	{
		if ( !$entry_list || count ( $entry_list ) <= 0 ) return;
		foreach ( $entry_list as $entry )
		{
			$id = $entry->getId();
			if ( ! key_exists( $id , $aggrigate_id_list ) ) $aggrigate_id_list[]= $id;
		}
	}
}
?>