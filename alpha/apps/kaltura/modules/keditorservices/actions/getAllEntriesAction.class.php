<?php
require_once ( "defKeditorservicesAction.class.php");
class getAllEntriesAction extends defKeditorservicesAction
{
	const LIST_TYPE_KSHOW = 1 ;
	const LIST_TYPE_KUSER = 2 ;
	const LIST_TYPE_ROUGHCUT = 4 ;
	const LIST_TYPE_EPISODE = 8 ;
	const LIST_TYPE_ALL = 15;
	
	protected function executeImpl ( kshow $kshow, entry &$entry )
	{
		$list_type = $this->getP ( "list_type" , self::LIST_TYPE_ALL );
		
		$kshow_entry_list = array();
		$kuser_entry_list = array();
		
		if ( $list_type & self::LIST_TYPE_KSHOW )
		{
			$c = new Criteria();
			$c->add ( entryPeer::TYPE , entry::ENTRY_TYPE_MEDIACLIP );
			$c->add ( entryPeer::MEDIA_TYPE , entry::ENTRY_MEDIA_TYPE_SHOW , Criteria::NOT_EQUAL );
			$c->add ( entryPeer::KSHOW_ID , $this->kshow_id );
			$kshow_entry_list = entryPeer::doSelectJoinkuser( $c );
		}

		if ( $list_type & self::LIST_TYPE_KUSER )
		{
			$c = new Criteria();
			$c->add ( entryPeer::TYPE , entry::ENTRY_TYPE_MEDIACLIP );
			$c->add ( entryPeer::MEDIA_TYPE , entry::ENTRY_MEDIA_TYPE_SHOW , Criteria::NOT_EQUAL );
			$c->add ( entryPeer::KUSER_ID , $this->getLoggedInUserIds(), Criteria::IN  );
			$kuser_entry_list = entryPeer::doSelectJoinkuser( $c );
		}		

		if ( $list_type & self::LIST_TYPE_EPISODE )
		{
			if ( $kshow->getEpisodeId() )
			{
				// episode_id will point to the "parent" kshow
				// fetch the entries of the parent kshow
				$c = new Criteria();
				$c->add ( entryPeer::TYPE , entry::ENTRY_TYPE_MEDIACLIP );
				$c->add ( entryPeer::MEDIA_TYPE , entry::ENTRY_MEDIA_TYPE_SHOW , Criteria::NOT_EQUAL );
				$c->add ( entryPeer::KSHOW_ID , $kshow->getEpisodeId() );
				$parent_kshow_entries = entryPeer::doSelectJoinkuser( $c );
				if ( count ( $parent_kshow_entries) )
				{
					$kshow_entry_list = kArray::append  ( $kshow_entry_list , $parent_kshow_entries );
				}			
			}
		}
		
		// fetch all entries that were used in the roughcut - those of other kusers 
		// - appeared under kuser_entry_list when someone else logged in

		if ( $list_type & self::LIST_TYPE_ROUGHCUT )
		{
			if ( $kshow->getHasRoughcut() )
			{
				$roughcut_file_name =  $entry->getDataPath();
				
				$entry_ids_from_roughcut = myFlvStreamer::getAllAssetsIds ( $roughcut_file_name );
				
				$final_id_list = array();
				foreach ( $entry_ids_from_roughcut as $id )
				{
					$found = false;
					foreach ( $kshow_entry_list as $entry )
					{
						if ( $entry->getId() == $id )
						{
							$found = true; 
							break;
						}
					}
					if ( !$found )	$final_id_list[] = $id;
				}
				
				$c = new Criteria();
				$c->add ( entryPeer::ID , $final_id_list , Criteria::IN );
				$extra_entries = entryPeer::doSelectJoinkuser( $c );
				
				// merge the 2 lists into 1:
				$kshow_entry_list = kArray::append  ( $kshow_entry_list , $extra_entries );
			}
		}
		
		$this->kshow_entry_list = $kshow_entry_list;
		$this->kuser_entry_list = $kuser_entry_list;
		
	}
	
	protected function noSuchKshow ( $kshow_id )
	{
		$this->kshow_entry_list = array ();
		$this->kuser_entry_list = array ();
	}

}

?>