<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class showtimeAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();

		$start = microtime(true);
		$limit = 7;
		
		// new partners
		$c = new Criteria();
		$this->partner_count = PartnerPeer::doCount ( $c ); // count before setting the limit
		$c->setLimit( $limit );
		$c->addDescendingOrderByColumn( PartnerPeer::CREATED_AT );
		$newest_partners = PartnerPeer::doSelect( $c );
//		$new_partners_ids = self::getIds( $new_partners );
		// fetch stats for these partners
		// TODO - if new - what statistics could it have ??	
//		fdb::populateObjects( $newest_partners , new PartnerStatsPeer() , "id" , "partnerStats" , false ,"partnerId");
		$exclude = $this->getP ( "exclude");
		if( $exclude ) self::addToExceludeList ( $exclude );
		$exclude_list = self::getExceludeList();
		// most viewed
		$c = new Criteria();
		$c->add ( PartnerStatsPeer::PARTNER_ID , $exclude_list , Criteria::NOT_IN );
		$c->setLimit ( $limit );
		$c->addDescendingOrderByColumn( PartnerStatsPeer::VIEWS );
		$stats_most_views = PartnerStatsPeer::doSelect( $c );
		$most_views = self::getPartnerListFromStats ( $stats_most_views );
		 
		// most entries 
		$c = new Criteria();
		$c->add ( PartnerStatsPeer::PARTNER_ID , $exclude_list , Criteria::NOT_IN );
		$c->setLimit ( $limit );
		$c->addDescendingOrderByColumn( PartnerStatsPeer::ENTRIES );
		$stats_most_entries = PartnerStatsPeer::doSelect( $c );
		$most_entries = self::getPartnerListFromStats ( $stats_most_entries );
		
		$end= microtime(true);

		$this->newest_partners = $newest_partners;
		$this->most_views = $most_views;
		$this->most_entries = $most_entries;
		
		$this->bench = $end - $start;
	}

	private static function getIds ( $list , $func_name = null )
	{
		if( ! $list ) return null;
		$ids = array ();
		foreach ( $list  as $elem )
		{
			if ( $func_name == null )
				$ids[] = $elem->getId();
			else
				$ids[] = call_user_func( array ( $elem , "get" . $func_name ) );
		}
		return $ids;
	}
		
	private static function attachStats ( $partner_list , $stats_list )
	{
		// build stats map
		$partners_map = array();

		foreach ( $partner_list as $partner )
		{
			$partners_map[$partner->getId()] = $partner;
		}
		
		$ordered_partners = array();
		foreach ( $stats_list as $stats )
		{
			$p = $partners_map[$stats->getPartnerId()];
			$p->setPartnerStats ( $stats );
			$ordered_partners[] = $p;
		}

		return 	$ordered_partners;
	}
	
	private static function getPartnerListFromStats ( $stats_list )
	{
		$ids = self::getIds( $stats_list , "PartnerId" );
		$partners = PartnerPeer::retrieveByPKs( $ids );
		$ordered_partners = self::attachStats ( $partners , $stats_list );
		$partners_order_according_to_stats = array();
		return $ordered_partners;
	}
	
	private static function getExceludeList()
	{
		$exclude_list = array ( 0 , 1, 5, 8 , 10, 100 , 300 );
		$from_file = @unserialize( @file_get_contents( self::getExcludeFileName() ) );
		if ( $from_file )  return array_merge( $exclude_list , array_keys( $from_file ) );
		else return $exclude_list;
	}
	
	private static function addToExceludeList( $ids )
	{
		$exclude_list = explode ( "," , $ids );
		$from_file = @unserialize( @file_get_contents( self::getExcludeFileName()) );
		if ( ! $from_file ) $from_file = array();
		foreach ( $exclude_list as $id )
		{
			$clean_id = trim($id) ;
			if ( substr ( $clean_id, 0 ,1 ) == "-" )
			{
				// remove from the list on disk
				unset ( $from_file[substr ( $clean_id, 1 )]);			
			}
			elseif ( substr ( $clean_id, 0 ,1 ) == "+" )
			{
				$from_file[substr ( $clean_id, 1 )]=1;			
			}
			else
			{
				$from_file[$clean_id]=1;
			}
		}
		
		file_put_contents(self::getExcludeFileName(), serialize($from_file) ); // sync - OK
	}	
	
	private static function getExcludeFileName()
	{
		return myContentStorage::getFSContentRootPath() . "/content/showtime_exclude_list" ; 
	}
}

?>