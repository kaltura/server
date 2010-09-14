<?php
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

class deleteKshowAction extends kalturaSystemAction
{
	/**
	 * 
select kshow.id,concat('http://www.kaltura.com/index.php/browse/bands?band_id=',indexed_custom_data_1),concat('http://profile.myspace.com/index.cfm?fuseaction=user.viewpr
ofile&friendID=',indexed_custom_data_1) ,  kuser.screen_name , indexed_custom_data_1  from kshow ,kuser where kshow.partner_id=5 AND kuser.id=kshow.producer_id AND kshow.
id>=10815  order by kshow.id ;
~

	 */
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		$kshow_id = $this->getRequestParameter( "kshow_id" , null );
		$band_id = $this->getRequestParameter( "band_id" , null );
		$kuser_name = $this->getRequestParameter( "kuser_name" , null );
		
		$this->other_kshows_by_producer = null;
		
		$error = "";
		
		$kshow = null;
		$kuser = null;
		$entries = null;
		
		$this->kuser_count = 0;
		
		$should_delete = $this->getRequestParameter( "deleteme" , "false" ) == "true" ;
		if ( $kuser_name )
		{
			$c = new Criteria();
			$c->add ( kuserPeer::SCREEN_NAME , "%" . $kuser_name . "%" , Criteria::LIKE );
			$this->kuser_count = kuserPeer::doCount ( $c );
			$kuser = kuserPeer::doSelectOne ( $c );
			
			if ( $kuser )
			{
				$this->other_kshows_by_producer = $this->getKshowsForKuser ( $kuser , null );
			}
			else
			{
				$error .= "Cannot find kuser with name [$kuser_name]<br>";
			}
			
			$other_kshow_count = count ( $this->other_kshows_by_producer );
			if (  $other_kshow_count < 1 )
			{
				// kuser has no kshow - delete him !
				if ( $should_delete )
				{
					$kuser->delete();
				}
			}
			else if ( $other_kshow_count == 1 )
			{
				$kshow_id = $this->other_kshows_by_producer[0]->getId();
			}
			else
			{
				// kuser has more than one kshow - let user choose 
				$error .= "[$kuser_name] has ($other_kshow_count) shows.<br>";
			}
		}
		
		if ( $band_id )
		{
			$c = new Criteria();
			$c->add ( kshowPeer::INDEXED_CUSTOM_DATA_1 , $band_id );
			$c->add ( kshowPeer::PARTNER_ID , 5 );
			$kshow = kshowPeer::doSelectOne( $c );
		}
		else if ( $kshow_id )
		{
			$kshow = kshowPeer::retrieveByPK( $kshow_id ); 
		}
		
		if ( $kshow )
		{
			if ( ! $kuser )		$kuser = kuserPeer::retrieveByPK( $kshow->getProducerId() );
			if ( $kuser )
			{
				$this->other_kshows_by_producer = $this->getKshowsForKuser ( $kuser , $kshow );
				
				if ( $should_delete )
				{
					if ( count ( $this->other_kshows_by_producer ) == 0 )
					{
						$kuser->delete();
					}
				}
			}
			
			$entries = $kshow->getEntrys ();
			
			if ( $should_delete )
			{
				$id_list = array();
				foreach ( $entries as $entry )
				{
					$id_list[] = $entry->getId();
				}
				
				if ( $id_list )
				{
					$d = new Criteria();
					$d->add ( entryPeer::ID , $id_list , Criteria::IN );
					entryPeer::doDelete( $d );
				}
			}
			
			if ( $should_delete )
			{
				$kshow->delete();
			}
			
		}
		else
		{
			$error .= "Cannot find kshow [$kshow_id]<br>";
		}
		
		
		$this->kshow_id = $kshow_id;
		$this->kuser_name = $kuser_name;
		$this->kshow = $kshow;
		$this->kuser = $kuser;
		$this->entries = $entries; 	
		$this->should_delete = $should_delete;	

		$this->error = $error; 
	}
	
	private function getKshowsForKuser ( $kuser , $kshow )
	{
		
		$c = new Criteria();
		$c->add ( kshowPeer::PRODUCER_ID , $kuser->getId() );
		if ( $kshow ) $c->add ( KshowPeer::ID , $kshow->getId(), Criteria::NOT_EQUAL );
		$other_kshows_by_producer = kshowPeer::doSelect( $c );
		
		return $other_kshows_by_producer;
						
	}
}
?>