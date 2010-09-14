<?php
require_once ( "kalturaSystemAction.class.php" );

class viewDashBoardAction extends kalturaSystemAction
{
	
	/**
	 * Gives a system applicative snapsot
	 */
	public function execute()
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		
		$this->forceSystemAuthentication();
		
		$partner_id =  $this->getRequestParameter('partner_id', -1 );
		if ( $partner_id >= 0 )
		{
			myPartnerUtils::applyPartnerFilters( $partner_id );
		}

		$this->partner_id = $partner_id;
				
		$limit = $this->getRequestParameter( 'limit' , '30');
		if ( $limit > 300 ) $limit  = 300;
		
		$bands_only = $this->getRequestParameter( "bands" , false ) != null;
		$modified_only = $this->getRequestParameter( "modified" , false ) != null;

		$this->bands_only = $bands_only;
		$this->modified_only = $modified_only;
		
		
		$this->kshows_with_new_entries = $modified_only ? dashboardUtils::getUpdatedKshows( ) : null;

		$yesterday = mktime(0, 0, 0, date("m"), date("d")-1,   date("Y"));
		$lastweek = mktime(0, 0, 0, date("m"), date("d")-7,   date("Y"));
		
		$query_esterday =  date('Y-m-d', $yesterday);
		$query_lastweek =  date('Y-m-d', $lastweek);
		
		$modified_band_ids = $modified_only ? array_keys( $this->kshows_with_new_entries ) : null;
		 
		if ( $modified_only )
		{
			// TODO - this chunk was copied from the code bellow with minor changes - generalize !
			
			$c = new Criteria();
// 			$c->add ( kshowPeer::ID , $modified_band_ids , Criteria::IN ); // search only the given IDs
			$this->bandsOnly ( $bands_only , $modified_band_ids ,  $c , kshowPeer::PARTNER_ID );
			$this->kshow_count = kshowPeer::doCount( $c );
			
			$criterion = $c->getNewCriterion(kshowPeer::CREATED_AT , $query_esterday , Criteria::GREATER_EQUAL  );
			$c->add($criterion);
			$this->kshow_count1 = kshowPeer::doCount( $c );
	
			$criterion = $c->getNewCriterion(kshowPeer::CREATED_AT , $query_lastweek , Criteria::GREATER_EQUAL  );
			$c->add($criterion);
			$this->kshow_count7 = kshowPeer::doCount( $c );
			
		    $c->setLimit( $limit );
			//$c->hints = array(kshowPeer::TABLE_NAME => "created_at_index");
		    $c->addDescendingOrderByColumn( kshowPeer::CREATED_AT );
			$c->remove( kshowPeer::CREATED_AT );
			$c->addJoin(kshowPeer::PRODUCER_ID, kuserPeer::ID, Criteria::LEFT_JOIN);

			$this->kshows = kshowPeer::doSelectJoinkuser($c);			
			$this->bands_only = $bands_only;
			
			$this->entry_count = 0;
			$this->entry_count1 = 0;
			$this->entry_count7 = 0;
			$this->entries = array();
			
			$this->kuser_count = 0;
			$this->kuser_count1 = 0;
			$this->kuser_count7 = 0;
			$this->kusers = array();
			
			dashboardUtils::updateKshowsRoughcutCount ( $this->kshows );
						
			return sfView::SUCCESS;			
		}
		
		$c = new Criteria();
		$this->bandsOnly ( $bands_only , $modified_band_ids , $c , kshowPeer::PARTNER_ID );
		$this->kshow_count = kshowPeer::doCount( $c );
		
		$d = new Criteria();
		$this->bandsOnly ( $bands_only , $modified_band_ids , $d , kshowPeer::PARTNER_ID );
		$criterion = $c->getNewCriterion(kshowPeer::CREATED_AT , $query_esterday , Criteria::GREATER_EQUAL  );
		$d->add($criterion);
		$this->kshow_count1 = kshowPeer::doCount( $d );

		$e = new Criteria();
		$this->bandsOnly ( $bands_only , $modified_band_ids , $e , kshowPeer::PARTNER_ID );
		$criterion = $c->getNewCriterion(kshowPeer::CREATED_AT , $query_lastweek , Criteria::GREATER_EQUAL  );
		$e->add($criterion);
		$this->kshow_count7 = kshowPeer::doCount( $e );
		
		//$this->kshow_count = kshowPeer::doCount( $c );
	    $c->setLimit( $limit );
		//$c->hints = array(kshowPeer::TABLE_NAME => "created_at_index");
	    $c->addDescendingOrderByColumn( kshowPeer::CREATED_AT );
		$c->addJoin(kshowPeer::PRODUCER_ID, kuserPeer::ID, Criteria::LEFT_JOIN);
	    $this->kshows = kshowPeer::doSelectJoinkuser($c);
	    
	    
	    
	    $c = new Criteria();
	    $this->bandsOnly ( $bands_only , $modified_band_ids , $c , entryPeer::PARTNER_ID );
		$this->entry_count = entryPeer::doCount( $c );
	    
		$d = new Criteria();
		$this->bandsOnly ( $bands_only , $modified_band_ids , $d , entryPeer::PARTNER_ID );
		$criterion = $c->getNewCriterion(entryPeer::CREATED_AT , $query_esterday, Criteria::GREATER_EQUAL  );
		$d->add($criterion);
		$this->entry_count1 = entryPeer::doCount( $d );

		$e = new Criteria();
		$this->bandsOnly ( $bands_only , $modified_band_ids , $e , entryPeer::PARTNER_ID );
		$criterion = $c->getNewCriterion(entryPeer::CREATED_AT , $query_lastweek , Criteria::GREATER_EQUAL  );
		$e->add($criterion);
		$this->entry_count7 = entryPeer::doCount( $e );
		
		$c->setLimit( $limit );
		//$c->hints = array(entryPeer::TABLE_NAME => "created_at_index");
		$c->addDescendingOrderByColumn( entryPeer::CREATED_AT );
		$c->add( entryPeer::TYPE, entry::ENTRY_TYPE_MEDIACLIP ); // we don't want entries that
//		$c->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, Criteria::INNER_JOIN);
//	    $c->addJoin(entryPeer::KSHOW_ID, kshowPeer::ID, Criteria::INNER_JOIN);
	    $this->entries = entryPeer::doSelectJoinAll($c);
	    
	    $c = new Criteria();
		$this->bandsOnly ( $bands_only , $modified_band_ids , $c , kuserPeer::PARTNER_ID );
	    $d = new Criteria();
	    $this->bandsOnly ( $bands_only , $modified_band_ids , $d , kuserPeer::PARTNER_ID );
		$criterion = $c->getNewCriterion(kuserPeer::CREATED_AT , $query_esterday , Criteria::GREATER_EQUAL  );
		$d->add($criterion);
		$this->kuser_count1 = kuserPeer::doCount( $d );

		$e = new Criteria();
		$this->bandsOnly ( $bands_only , $modified_band_ids , $e , kuserPeer::PARTNER_ID );
		$criterion = $c->getNewCriterion(kuserPeer::CREATED_AT , $query_lastweek , Criteria::GREATER_EQUAL  );
		$e->add($criterion);
		$this->kuser_count7 = kuserPeer::doCount( $e );
	    
	    $this->kuser_count = kuserPeer::doCount( $c );
	    $c->setLimit( $limit );
		$c->addDescendingOrderByColumn( kuserPeer::CREATED_AT );
		$this->kusers = kuserPeer::doSelect($c);

		dashboardUtils::updateKusersRoughcutCount ( $this->kusers );
		dashboardUtils::updateKshowsRoughcutCount ( $this->kshows );
	 
		return sfView::SUCCESS;
	}
	
	private function bandsOnly ( $bands_only  , $kshows_with_new_entries , $c , $partner_id )
	{
		return ; // the fiter will be done useing the criteriaFilter 
		$c->addAnd ( $partner_id , 5 , ( $bands_only ? Criteria::EQUAL : Criteria::NOT_EQUAL ) );
		if ( $kshows_with_new_entries != null )
		{
			$id_field = str_replace( ".PARTNER_ID" , ".ID" , $partner_id );
			$c->add ( $id_field , $kshows_with_new_entries , Criteria::IN ); // search only the given IDs
		}
	}

/*
	select 
		kshow_id , count(kshow_id) 
	from 
		entry,kshow  
	where 
		entry.kshow_id=kshow.id 
		AND 
			(entry.created_at > kshow.created_at +60) 
		AND 
			kshow.partner_id=5 
	group by kshow_id;
*/

}
?>