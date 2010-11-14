<?php
require_once ( "defPartnerservices2Action.class.php");

class mrssAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "mRss",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"filter" => array ("type" => "entryFilter", "desc" => "")
						),
					"optional" => array (
						"page_size" => array ("type" => "integer", "default" => 10, "desc" => ""),
						"page" => array ("type" => "boolean", "default" => 1, "desc" => ""),
						)
					),
				"out" => array (
					),
				"errors" => array (
				)
			);
	}

	// the ticket will be ignored and the security system will be implemented inside the function
	protected function ticketType()	{		return self::REQUIED_TICKET_NONE;	}
	
	protected function needKuserFromPuser()	{		return self::KUSER_DATA_NO_KUSER;	}

	protected function setExtraFilters ( entryFilter &$fields_set )	
	{
		$fields_set->set( "_in_type" , entryType::MEDIA_CLIP ) ;//. "," . entryType::MIX );		
		if ( ! $fields_set->get( "_order_by" ) )
		{
			$fields_set->set( "_order_by" , "-created_at" );
		}
	}

	// TODO - detache from the base class defPartnerservices2Action
	/**
	 * This is not a regular service.
	 * Because the caller is not a partner but rather a 3rd party provider that wishs to query our system,
	 * The security is slightly different and the respons is in the format of mRss which is related to entries only.
	 */
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		header ( "Content-Type: text/xml; charset=utf-8" );
		
		// TODO -  verify permissions for viewing lists
		// validate the ks of the caller 
		$code = $this->getP ( "code" );
		if ( $code != 'fsalh5423a43g' ) 
		{	
			return "<xml></xml>";
			die();
		}  
				
		$detailed = $this->getP ( "detailed" , false );
		$limit = $this->getP ( "page_size" , 100 );
		$limit = $this->maxPageSize ( $limit );

		$operated_partner_id = $this->getP ( "operated_partner_id" );
		
		$page = $this->getP ( "page" , 1 );

		$offset = ($page-1)* $limit;

//		kuserPeer::setUseCriteriaFilter( false );
		if ( $operated_partner_id )
		{
			entryPeer::setUseCriteriaFilter( true );
		}
		else
		{
			entryPeer::setUseCriteriaFilter( false );
		}

// FOR now - display only 2 partners
		// 2460 - dorimedia
		$partner_list = array ( 593, 2460 );
		 
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$c->addAnd ( entryPeer::STATUS , entryStatus::READY );
		
		// for now display only entries that are part of the kaltura network
//		$c->addAnd ( entryPeer::DISPLAY_IN_SEARCH , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK );
		
		// filter
		$filter = new entryFilter(  );
		$fields_set = $filter->fillObjectFromRequest( $this->getInputParams() , "filter_" , null );

		$this->setExtraFilters ( $filter );
	
		$offset = ($page - 1) * $limit;
		$c->setLimit($limit);

		if($offset > 0)
			$c->setOffset($offset);
		
		$filter->attachToCriteria( $c );
		//if ($order_by != -1) entryPeer::setOrder( $c , $order_by );

		$c->addAnd ( entryPeer::PARTNER_ID , $partner_list , Criteria::IN ); 
		
		$start_1 = microtime ( true );

		if ( $detailed )
		{
			// for some entry types - there are no kshow or kusers - don't join even when detailed
			if ( $this->joinOnDetailed () )	$list = entryPeer::doSelectJoinAll( $c );
			else $list = entryPeer::doSelect( $c );
			$level = objectWrapperBase::DETAIL_LEVEL_DETAILED ;
		}
		else
		{
			$list = entryPeer::doSelect( $c );
			$level = objectWrapperBase::DETAIL_LEVEL_REGULAR ;
		}
		$count = $c->getRecordsCount();
		
$end_1 = microtime ( true );

		KalturaLog::log ( "benchmark db: [" . ( $end_1 - $start_1 ) . "]" );
		
		$result_count = count ( $list );
$start_2 = microtime ( true );
		$mrss_renderer = new kalturaRssRenderer ( kalturaRssRenderer::TYPE_TABOOLA ); 
		$str = $mrss_renderer->renderMrssFeed( $list , $page , $result_count );
$end_2 = microtime ( true );


		KalturaLog::log ( "benchmark render: [" . ( $end_2 - $start_2 ) . "]" );
		echo $str;
		
		// don't return to the rest of the implementation - the base class manipulates the content.
		die();
	}
	
	protected function maxPageSize ( $limit )
	{
		return min ( $limit , 200 );
	}
}
?>