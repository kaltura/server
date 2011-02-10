<?php
/**
 * @package api
 * @subpackage ps2
 */
class listkshowsAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 	
			array (
				"display_name" => "listKShows",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"filter" => array ("type" => "kshowFilter", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => ""),
						"page_size" => array ("type" => "integer", "default" => 10, "desc" => ""),
						"page" => array ("type" => "boolean", "default" => 1, "desc" => ""),
						"use_filter_puser_id" => array ("type" => "boolean", "desc" => ""),
						)
					),
				"out" => array (
					"count" => array ("type" => "integer", "desc" => ""),
					"page_size" => array ("type" => "integer", "desc" => ""),
					"page" => array ("type" => "integer", "desc" => ""),
					"kshows" => array ("type" => "*kshow", "desc" => ""),
					"user" => array ("type" => "kuser", "desc" => ""),
					),
				"errors" => array (
				)
			); 
	}
	
	protected function ticketType()	{		return self::REQUIED_TICKET_ADMIN;	}
		
	protected function needKuserFromPuser ( )	{		return self::KUSER_DATA_KUSER_DATA;	}
		
	protected function setExtraFilters ( kshowFilter &$fields_set )
	{
		
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		

		// TODO -  verify permissions for viewing lists 

		$detailed = $this->getP ( "detailed" , false );
		$limit = $this->getP ( "page_size" , 10 );
		$page = $this->getP ( "page" , 1 );		
		//$order_by = int( $this->getP ( "order_by" , -1 ) );
		
		$puser_kuser = null;
		$use_filter_puser_id = $this->getP ( "use_filter_puser_id" , 1 );
		if ( $use_filter_puser_id == "false" ) $use_filter_puser_id = false;
		

		 
		$offset = ($page-1)* $limit;

		kuserPeer::setUseCriteriaFilter( false ); 
		entryPeer::setUseCriteriaFilter( false );

		$c = new Criteria();
				
		// filter		
		$filter = new kshowFilter(  );
		$fields_set = $filter->fillObjectFromRequest( $this->getInputParams() , "filter_" , null );
		
		$this->setExtraFilters ( $filter );
		
		if ( $use_filter_puser_id )
		{
			// if so - assume the producer_id is infact a puser_id and the kuser_id should be retrieved
			$target_puser_id = $filter->get ( "_eq_producer_id" );
			//$this->getP ( "filter__eq_producer_id" );
			if ( $target_puser_id )		
			{
				// TODO - shoud we use the subp_id to retrieve the puser_kuser ?
				$puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid( $partner_id , null /*$subp_id*/, $target_puser_id , false);
				if ( $puser_kuser )
				{
					$filter->set ( "_eq_producer_id" ,  $puser_kuser->getkuserId() );
				//$this->setP ( "filter__eq_producer_id" , $puser_kuser->getkuserId() );
				}
			}
		}		
		
		$filter->attachToCriteria( $c );
		//if ($order_by != -1) kshowPeer::setOrder( $c , $order_by );
		$count = kshowPeer::doCount( $c );

		$offset = ($page-1)* $limit;
		
		
		$c->setLimit( $limit );
		
		if ( $offset > 0 )
		{
			$c->setOffset( $offset );
		}
				
		if ( $detailed )
		{
			$list = kshowPeer::doSelectJoinAll( $c );
			$level = objectWrapperBase::DETAIL_LEVEL_DETAILED ;
			// will have to populate the show_entry before according to the ids
			fdb::populateObjects( $list , new entryPeer() , "showentryid" , "showentry" , false ); 
		}
		else
		{
			$list = kshowPeer::doSelect( $c );
			$level = objectWrapperBase::DETAIL_LEVEL_REGULAR ;
			// will have to populate the show_entry before according to the ids - we display the thumbnail from the showentry			
			fdb::populateObjects( $list , new entryPeer() , "showentryid" , "showentry" , false );
		}

		$this->addMsg ( "count" , $count );
		$this->addMsg ( "page_size" , $limit );
		$this->addMsg ( "page" , $page );

		$wrapper =  objectWrapperBase::getWrapperClass( $list  , $level );
		$this->addMsg ( "kshows" , $wrapper ) ;
		if ( $use_filter_puser_id )
		{
			$this->addMsg ( "user" , objectWrapperBase::getWrapperClass( $puser_kuser  , objectWrapperBase::DETAIL_LEVEL_REGULAR ) );
		} 
		

/*
		$i=0;
		foreach ( $list as $kshow )
		{
			$i++;
			$wrapper =  objectWrapperBase::getWrapperClass( $kshow  , $level );
			$this->addMsg ( "kshow$i" , $wrapper ) ;
		}
*/

//		echo "bbb count: " . count ($list );
	
		
//		echo "ccc";
		
		//$this->addMsg ( "kshows" , $wrapper ) ;
		

	}
}
?>