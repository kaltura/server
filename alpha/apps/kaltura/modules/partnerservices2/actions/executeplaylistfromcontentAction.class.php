<?php
require_once ( "defPartnerservices2Action.class.php");

class executeplaylistfromcontentAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "executePlaylistFromContent",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"playlist" => array ("type" => "entry", "desc" => "a playlist object filled with at least the mediaType & dataContent")
						),
					"optional" => array (
						"fp" => array ("type" => "string", "desc" => "filter prefix used for all the following filters") ,
						"filter1" => array ("type" => "entryFilter", "desc" => "") ,
						"filter2" => array ("type" => "entryFilter", "desc" => "") ,
						"filter3" => array ("type" => "entryFilter", "desc" => "") ,
						"filter4" => array ("type" => "entryFilter", "desc" => "") ,
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
					"entries" => array ("type" => "*entry", "desc" => ""),
					"user" => array ("type" => "PuserKuser", "desc" => ""),
					),
				"errors" => array (
				)
			);
	}

	const MAX_FILTER_COUNT = 10;
	
	protected function ticketType()
	{
		return self::REQUIED_TICKET_REGULAR;
	}

	protected function setExtraFilters ( entryFilter &$fields_set )	{	}
	
	protected function joinOnDetailed () { return true;}

	protected function getObjectPrefix () { return "entries"; } 

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// TODO -  verify permissions for viewing lists

		$detailed = $this->getP ( "detailed" , false );
		$limit = $this->getP ( "page_size" , 10 );
		$limit = $this->maxPageSize ( $limit );

		$page = $this->getP ( "page" , 1 );

		$user_filter_prefix = $this->getP ( "fp" , "filter" );
		
		$offset = ($page-1)* $limit;

		// TODO - should limit search to partner ??
//		kuserPeer::setUseCriteriaFilter( false );
//		entryPeer::setUseCriteriaFilter( false );

		$input_params = $this->getInputParams();
		
		// fill the playlist (infact only the mediaType and contentData are important
		$playlist = new entry();
		$playlist->setType ( entry::ENTRY_TYPE_PLAYLIST ); // prepare the playlist type before filling from request
		$obj_wrapper = objectWrapperBase::getWrapperClass( $playlist , 0 );
		
		$playlist->setMediaType ( $this->getP ( "playlist_mediaType" ) );
		$data_content = $this->getP ( "playlist_dataContent" );
		
		$playlist->setDataContent( $data_content );
		
/*		
		$updateable_fields = $obj_wrapper->getUpdateableFields() ;
		$fields_modified = baseObjectUtils::fillObjectFromMapOrderedByFields( $input_params , $playlist , "playlist_" , 
			$updateable_fields , BasePeer::TYPE_PHPNAME ,false );
	*/	
		// rest is similar to the executeplaylist service		
		$extra_filters = array();
		for ( $i=1 ; $i< self::MAX_FILTER_COUNT ; $i++ )
		{
			// filter
			$extra_filter = new entryFilter(  );
			
			$fields_set = $extra_filter->fillObjectFromRequest( $input_params , "{$user_filter_prefix}{$i}_" , null );
			$extra_filters[$i] = $extra_filter;
		}
	
		$entry_list = myPlaylistUtils::executePlaylist ( $partner_id , $playlist , $extra_filters , $detailed );

		myEntryUtils::updatePuserIdsForEntries ( $entry_list );
		
		$level = $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR ;
		$wrapper =  objectWrapperBase::getWrapperClass( $entry_list  , $level );
		$this->addMsg ( "count" , count ( $entry_list )) ;
		$this->addMsg ( $this->getObjectPrefix() , $wrapper ) ;
		
	}
}
?>