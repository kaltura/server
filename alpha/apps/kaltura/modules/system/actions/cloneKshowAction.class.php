<?php
require_once ( "kalturaSystemAction.class.php" );

class cloneKshowAction extends kalturaSystemAction
{

	public function execute()
	{
		$this->forceSystemAuthentication();

		$source_kshow_id = $this->getP ( "source_kshow_id" );
		$target_kshow_id = $this->getP ( "target_kshow_id" );
		$kuser_names = $this->getP ( "kuser_names" );

		$reset = $this->getP ( "reset" );
		if ( $reset )
		{
			$source_kshow_id = null;
			$target_kshow_id = null;
			$kuser_names = null;
		}
		
		$mode = 0;// view
		if ( $source_kshow_id && $target_kshow_id && $kuser_names )
		{
			$mode = 1; // review
			$list_of_kuser_names = explode ( "," , $kuser_names );
			foreach ( $list_of_kuser_names  as &$name )
			{
				$name = trim($name);
			}

			$source_kshow = kshowPeer::retrieveByPK( $source_kshow_id ) ;
			$target_kshow = kshowPeer::retrieveByPK( $target_kshow_id ) ;

			$target_partner_id = $target_kshow->getPartnerId();
			$target_subp_id = $target_kshow->getSubpId();

			$c = new Criteria();
			// select only the kusers of the correct partner_id
			$c->add ( kuserPeer::SCREEN_NAME , $list_of_kuser_names , Criteria::IN );
			$c->setLimit( 10 );
			//$c->add ( kuserPeer::PARTNER_ID , $target_partner_id );
			$list_of_kusers = kuserPeer::doSelect( $c );
			$producer = kuserPeer::retrieveByPK( $target_kshow->getProducerId());;
			$list_of_kusers[] = $producer;

			$c->add ( kuserPeer::PARTNER_ID , $target_partner_id );
			$list_of_valid_kusers = kuserPeer::doSelect( $c );
			$list_of_valid_kusers[] = $producer;
			
			$c = new Criteria();
			$c->add ( entryPeer::KSHOW_ID , $source_kshow_id );
			$c->add ( entryPeer::TYPE , entry::ENTRY_TYPE_MEDIACLIP );
			$c->add ( entryPeer::STATUS , entry::ENTRY_STATUS_READY );
			$entries = entryPeer::doSelectJoinAll( $c );
			
			$entry_kusers = array();
			// assign each entry to a kuser
			foreach ( $entries as $entry )
			{
				$place_in_array = count ( $entry_kusers ) % count ($list_of_valid_kusers );
				$kuser = $list_of_valid_kusers[ $place_in_array ];
				$entry_kusers[$entry->getId()] = $kuser->getId();	
			}
			
			$clone = $this->getP ( "clone" );
			if ( $clone == 'true') 
			{
				$mode = 2; // clone
				
				$entry_id_map = array(); 	// will be used to map the source->target entries
				$entry_cache = array ();	// will be used to cache all relevat entries
				
				$new_entry_list = array();
				$failed_entry_list = array();
				foreach ( $entries as $entry )
				{
					try
					{
						$kuser_id = $entry_kusers[$entry->getId()] ;
						$override_fields = $entry->copy();
						$override_fields->setPartnerId ( $target_kshow->getPartnerId() );
						$override_fields->setSubpId( $target_kshow->getSubpId());
						$override_fields->setKuserId( $kuser_id );
						
						$new_entry = myEntryUtils::deepClone( $entry , $target_kshow_id , $override_fields ,false );
						$new_entry_list[] = $new_entry;
						// will help fix the metadata entries
						$entry_id_map [$entry->getId()] = $new_entry->getId();

						$entry_cache[$entry->getId()]=$entry;
						$entry_cache[$new_entry->getId()]=$new_entry;
					}
					catch ( Exception $ex )
					{
						$failed_entry_list[] = $entry; 
					}

//					echo "entry [{$entry->getId()}] copied<br>"; flush();
				}
				
				// now clone the show_entry
				$new_show_entry = $target_kshow->getShowEntry();
				myEntryUtils::deepCloneShowEntry ( $source_kshow->getShowEntry() , $new_show_entry , $entry_id_map , $entry_cache ) ;
				$new_entry_list[] = $new_show_entry;
				$entries = $new_entry_list;
				$entry_kusers = null;
			}
			
//			echo "ended!<bR>";			flush();
		}
		
		$this->source_kshow_id = @$source_kshow_id;
		$this->target_kshow_id = @$target_kshow_id;
		$this->partner_id = @$target_partner_id;
		$this->source_kshow = @$source_kshow;
		$this->target_kshow = @$target_kshow;
		$this->kuser_names = @$kuser_names;
		$this->list_of_kusers = @$list_of_kusers;
		$this->entries = @$entries;
		$this->mode = $mode;
		$this->entry_kusers = @$entry_kusers;
		
//		echo "going to template!<bR>";		flush();
	}
}
?>