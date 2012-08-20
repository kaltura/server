<?php
/**
 * @package api
 * @subpackage ps2
 */
class listentriesAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "listEntries",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"filter" => array ("type" => "entryFilter", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => ""),
						"detailed_fields" => array ("type" => "string", "desc" => "A list of fields (that do not belong to the level of details) to add to the entry - separated by ','"),
						"page_size" => array ("type" => "integer", "default" => 10, "desc" => ""),
						"page" => array ("type" => "integer", "default" => 1, "desc" => ""),
						"use_filter_puser_id" => array ("type" => "boolean", "desc" => ""),
						)
					),
				"out" => array (
					"count" => array ("type" => "integer", "desc" => ""),
					"page_size" => array ("type" => "integer", "desc" => ""),
					"page" => array ("type" => "integer", "desc" => ""),
					"entries" => array ("type" => "*entry", "desc" => ""),
					"user" => array ("type" => "PuserKuser", "desc" => ""),
					),
				"errors" => array (
				)
			);
	}

	protected function ticketType()
	{
		return self::REQUIED_TICKET_ADMIN;
	}

	protected function setExtraFilters ( entryFilter &$fields_set )	{	}
	
	protected function joinOnDetailed () { return true;}

	protected function getObjectPrefix () { return "entries"; } 

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		

		// TODO -  verify permissions for viewing lists

		$detailed = $this->getP ( "detailed" , false );
		$detailed_fields = $this->getP ( "detailed_fields" );
		
		$limit = $this->getP ( "page_size" , 10 );
		$limit = $this->maxPageSize ( $limit );

		$page = $this->getP ( "page" , 1 );

		$offset = ($page-1)* $limit;

		kuserPeer::setUseCriteriaFilter( false );		
		//entryPeer::setUseCriteriaFilter( false );

		$c = KalturaCriteria::create(entryPeer::OM_CLASS);

		// filter
		$filter = new entryFilter(  );
		$fields_set = $filter->fillObjectFromRequest( $this->getInputParams() , "filter_" , null );

		$this->setExtraFilters ( $filter );
		$filter->setPartnerSearchScope( baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE );

		$desired_status = "status:" . $filter->get ( "_eq_status" ) . "," . $filter->get ( "_in_status" );
		$display_deleted = $this->getP ( "display_deleted" , false ); 	
		if ( $display_deleted == "false" ) $display_deleted = false;	
		
		$pos = strpos ( $desired_status , entryStatus::DELETED );
		if ( $display_deleted || $pos !== false   )
		{
			entryPeer::allowDeletedInCriteriaFilter();	 
		}

		// hack for displaying pre-moderation 
		$moderation_status = $filter->get ( "_in_moderation_status" );
		if ( $moderation_status && 
			( strpos ( $moderation_status , "1,5" ) !== false  || strpos ( $moderation_status , "5,1" ) !== false ) )
		{
			// this is when the KMC requests the moderated entries
			$filter->set ( "_in_status" , $filter->get ( "_in_status" ) . ",5" ) ;  // add the status '5' 
		}
		
		$this->fixModerationStatusForBackwardCompatibility($filter);
				
		$puser_kuser = null;
		$use_filter_puser_id = $this->getP ( "use_filter_puser_id" , 1 );
		if ( $use_filter_puser_id == "false" ) $use_filter_puser_id = false;
		
		if ( $use_filter_puser_id )
		{
			// if so - assume the producer_id is infact a puser_id and the kuser_id should be retrieved
			$target_puser_id = $filter->get ( "_eq_user_id" );
			if ( $target_puser_id !== null )
			{
				$puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid( $partner_id , null /* $subp_id */, $target_puser_id , false);
				if ( $puser_kuser )
				{
					$filter->set ( "_eq_user_id" ,  $puser_kuser->getkuserId() );
					//	$this->setP ( "filter__eq_producer_id" , $puser_kuser->getkuserId() );
				}
			}
		}
	
		$offset = ($page - 1) * $limit;
		$c->setLimit($limit);

		if($offset > 0)
			$c->setOffset($offset);
		
		$filter->attachToCriteria( $c );

		// for some entry types - there are no kshow or kusers - don't join even when detailed
		if ( $this->joinOnDetailed () )	
			$list = entryPeer::doSelectJoinKuser( $c );
		else 
			$list = entryPeer::doSelect( $c );
		
		if ( $detailed )
		{
			$level = objectWrapperBase::DETAIL_LEVEL_DETAILED ;
		}
		else
		{
			$level = objectWrapperBase::DETAIL_LEVEL_REGULAR ;
		}
		
		$count = $c->getRecordsCount();
		
		$this->addMsg ( "count" , $count );
		$this->addMsg ( "page_size" , $limit );
		$this->addMsg ( "page" , $page );

		
		myEntryUtils::updatePuserIdsForEntries ( $list );
		
		
		if ( $detailed_fields )
		{
			$extra_fields = explode ( "," , $detailed_fields );
			$wrapper =  objectWrapperBase::getWrapperClass( $list  , $level , objectWrapperBase::DETAIL_VELOCITY_DEFAULT , 0 , $extra_fields );
		}
		else
		{
			$wrapper =  objectWrapperBase::getWrapperClass( $list  , $level );
		}
		$this->addMsg ( $this->getObjectPrefix() , $wrapper ) ;
		if ( $use_filter_puser_id )
		{
			$this->addMsg ( "user" , objectWrapperBase::getWrapperClass( $puser_kuser  , objectWrapperBase::DETAIL_LEVEL_REGULAR ) );
		}
	}
	
	/**
	 * backward compatibility after fixing entry moderation for api v3
	 * entry status 5, is changed to entry status 2 + moderation_status 1
	 * @param $filter
	 */
	function fixModerationStatusForBackwardCompatibility($filter)
	{
		// get the requested statuses
		$statuses = array();
		if ($filter->get ( "_in_status" ))
			$statuses = explode(",", $filter->get("_in_status")); 
			
		if ($filter->get ( "_eq_status" ))
			$statuses[] = $filter->get ( "_eq_status" );
		
		// we are fixing the old moderate status 
		if (in_array((string)entryStatus::MODERATE, $statuses))
		{
			$filter->set("_eq_status", ""); 
			$filter->set("_in_status", "");
			
			$i = array_search((string)entryStatus::MODERATE, $statuses);
			unset($statuses[$i]); // remove the moderate status
			
			$statuses[] = (string)entryStatus::READY; // add the ready status
			
			$filter->set("_in_status", implode(",", $statuses)); // set back to the filter
			
			// add the real moderation status
			
			$moderations_statues = array();
			if ($filter->get ( "_in_moderation_status" ))
				$moderations_statues = explode(",", $filter->get("_in_moderation_status")); 
			
			if ($filter->get ( "_eq_moderation_status" ))
				$moderations_statues[] = $filter->get ( "_eq_moderation_status" );
				
			$moderations_statues[] = (string)entry::ENTRY_MODERATION_STATUS_PENDING_MODERATION;
			$filter->set("_in_moderation_status",  implode(",", $moderations_statues));
			$filter->set("_eq_moderation_status", "");
		}
	}
}
