<?php
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

class investigateAction extends kalturaSystemAction
{
	/**
	 * Will investigate a single entry
	 */
	public function execute()
	{
		$this->forceSystemAuthentication();

		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		entryPeer::setUseCriteriaFilter( false );
		$this->result = NULL;
		
		$fast = ( $this->getRequestParameter("fast", "") != "" );
	 	$this->fast = $fast;
	 	
		$kshow_id = $this->getRequestParameter("kshow_id");
		$this->kshow_id = $kshow_id;
		$this->kshow = NULL;
		
		$entry_id = $this->getRequestParameter("entry_id");
		$this->entry_id = $entry_id;
		$this->entry = NULL;
		
		$this->error = $this->getRequestParameter( "error" );
		
		$this->bg_entry = NULL;
		
		if ( !empty ( $kshow_id ))
		{
			
			$c = new Criteria();
			$c->add ( kshowPeer::ID , $kshow_id );
			$kshows = kshowPeer::doSelect ( $c );
			
			$kshow = new kshow();
			
			if ( ! $kshows )
			{
				$this->result = "No kshow [$kshow_id] in DB";
				return;
			}
	
			$kshow_original = $kshows[0];
			$kshow_original->getShowEntry() ; // pre fetch
			$kshow_original->getIntro(); // pre fetch

			$this->kshow_original =$kshows[0];
			$this->kshow =  new genericObjectWrapper ($this->kshow_original , true );

			$alredy_exist_entries = array ( );
			$alredy_exist_entries[] = $kshow_original->getShowEntryId();
			if( $kshow_original->getIntroId()) $alredy_exist_entries[] = $kshow_original->getIntroId();
			
			$skin_obj = $this->kshow_original->getSkinObj();
			$bg_entry_id = $skin_obj->get ( "bg_entry_id");
			if ( $bg_entry_id )
			{
				$alredy_exist_entries[] = $bg_entry_id;
				$this->bg_entry =  new genericObjectWrapper ( entryPeer::retrieveByPK( $bg_entry_id ) , true );
			}
			
			$c = new Criteria();
			$c->add ( entryPeer::ID , $alredy_exist_entries  , Criteria::NOT_IN );
			$c->setLimit ( 100 );
			$this->kshow_entries = $this->kshow_original->getEntrysJoinKuser( $c );
			
			return;
			//return "KshowSuccess";
		}
		
		if ( empty ( $entry_id ))
		{
			return;
		}
		
		entryPeer::setUseCriteriaFilter( false );
		
		// from entry table
		$c = new Criteria();
		$c->add ( entryPeer::ID , $entry_id );
		//$entries = entryPeer::doSelectJoinAll ( $c );
		$entries = entryPeer::doSelect ( $c );
		if ( ! $entries )
		{
			$this->result = "No entry [$entry_id] in DB";
			return;
		}

		$this->entry = new genericObjectWrapper ( $entries[0] , true );

		// from conversion table
		$c = new Criteria();
		$c->add ( conversionPeer::ENTRY_ID , $entry_id );
		$original_conversions = conversionPeer::doSelect( $c );

		//$this->conversions = array() ; //
		$this->conversions = $original_conversions; //new genericObjectWrapper( $original_conversions );

		// find all relevant batches in DB
		// from batch_job table
		$c = new Criteria();
		//$c->add ( BatchJobPeer::DATA , "%\"entryId\";i:" . $entry_id . ";%" , Criteria::LIKE );
		$c->add ( BatchJobPeer::ENTRY_ID, $entry_id ); 
		$original_batch_jobs = BatchJobPeer::doSelect( $c );

		$this->batch_jobs  = $original_batch_jobs ; // new genericObjectWrapper( $original_batch_jobs );

		// use this as a refernece of all the directories
//		myBatchFileConverterServer::init( true );
		
		$entry_patttern = "/" . $entry_id . "\\..*/";
		$getFileData_method = array ( 'kFile' , 'getFileData' );
		$getFileDataWithContent_method = array ( 'kFile' , 'getFileDataWithContent' );

		// find all relevant files on disk
		$c = new Criteria();
		$c->add ( FileSyncPeer::OBJECT_TYPE , FileSyncObjectType::ENTRY );
		$c->add ( FileSyncPeer::OBJECT_ID , $entry_id );
		// order by OBJECT SUB TYPE
		$c->addAscendingOrderByColumn ( FileSyncPeer::OBJECT_SUB_TYPE );
		
		$this->file_syncs = FileSyncPeer::doSelect( $c );
		
		$file_sync_links = array();
		$flavors = flavorAssetPeer::retrieveByEntryId( $entry_id );
		$flavor_ids = array();
		$this->flavors =array();
		foreach ( $flavors as $f )
		{
			$flavor_ids[] = $f->getId();
			$f->getflavorParamsOutputs();
			$f->getflavorParams();
			$f->getmediaInfos();
			
			$this->flavors[]=$f;
		}
		 
		// find all relevant files on disk
		$c = new Criteria();
		$c->add ( FileSyncPeer::OBJECT_TYPE , FileSyncObjectType::FLAVOR_ASSET );
		$c->add ( FileSyncPeer::OBJECT_ID , $flavor_ids , Criteria::IN );
		// order by OBJECT SUB TYPE
		$c->addAscendingOrderByColumn ( FileSyncPeer::OBJECT_SUB_TYPE );
		
		$flavors_file_syncs = FileSyncPeer::doSelect( $c );
		
		$this->flavors_file_syncs =array();
		foreach ( $flavors as $flav )
		{
			foreach ( $flavors_file_syncs as $f )
			{
				if ( $f->getLinkedId() ) $file_sync_links[] = $f->getLinkedId();
				
				if ( $f->getObjectId() == $flav->getId() )
					$this->flavors_file_syncs[$flav->getId()][]=$f; 
			}
		}
		
		if ( $this->file_syncs )
		{
			$this->file_syncs_by_sub_type = array();
			foreach ( $this->file_syncs as $fs )
			{
				if ( $fs->getLinkedId() ) $file_sync_links[] = $fs->getLinkedId();
				$sub_type = $fs->getObjectSubType();
				if ( !isset($this->file_syncs_by_sub_type[$sub_type]))
				{
					// create the array 
					$this->file_syncs_by_sub_type[$sub_type]=array();
				}
				
				$this->file_syncs_by_sub_type[$sub_type][]=$fs;
			}
		}
		else
		{
			$this->file_syncs_by_sub_type = array();		
		}
	
		$file_sync_criteria = new Criteria();
		$file_sync_criteria->add ( FileSyncPeer::ID , $file_sync_links , Criteria::IN );
		$this->file_sync_links = FileSyncPeer::doSelect( $file_sync_criteria );
		
		$track_entry_c = new Criteria();
		$track_entry_c->add ( TrackEntryPeer::ENTRY_ID , $entry_id );
		$track_entry_list = TrackEntryPeer::doSelect ( $track_entry_c );
		$more_interesting_track_entries = array(); 
		foreach ( $track_entry_list as $track_entry )
		{
			if ( $track_entry->getTrackEventTypeId() == TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY ) 
				 $more_interesting_track_entries[] = $track_entry->getParam3Str() ; 
		}

		// add all the track_entry objects that are related (joined on PARAM_3_STR)
		$track_entry_c2 = new Criteria();
		$track_entry_c2->add ( TrackEntryPeer::TRACK_EVENT_TYPE_ID , TrackEntry::TRACK_ENTRY_EVENT_TYPE_UPLOADED_FILE );
		$track_entry_c2->add ( TrackEntryPeer::PARAM_3_STR , $more_interesting_track_entries , Criteria::IN );
		$track_entry_list2 = TrackEntryPeer::doSelect ( $track_entry_c2 );
		
		// first add the TRACK_ENTRY_EVENT_TYPE_UPLOADED_FILE - they most probably happend before the rest
		$this->track_entry_list = array_merge ( $track_entry_list2 , $track_entry_list   );
	}
	
}
?>