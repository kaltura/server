<?php
require_once ( "defPartnerservices2Action.class.php");

class getplayliststatsfromcontentAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "getPlaylistStatsFromContent",
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
					"playlist" => array ("type" => "entry" , "desc" => "a parsial playlist object with at leaset the count,countData and duration"),
					),
				"errors" => array (
				)
			);
	}

	const MAX_FILTER_COUNT = 10;
	
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
		
		// rest is similar to the executeplaylist service		
		$extra_filters = array();
		for ( $i=1 ; $i< self::MAX_FILTER_COUNT ; $i++ )
		{
			// filter
			$extra_filter = new entryFilter(  );
			
			$fields_set = $extra_filter->fillObjectFromRequest( $input_params , "{$user_filter_prefix}{$i}_" , null );
			$extra_filters[$i] = $extra_filter;
		}
	
		//$entry_list = myPlaylistUtils::executePlaylist ( $partner_id , $playlist , $extra_filters , $detailed );
		myPlaylistUtils::updatePlaylistStatistics ( $partner_id , $playlist );//, $extra_filters , $detailed );
		
		$level = $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR ;
		$wrapper =  objectWrapperBase::getWrapperClass( $playlist  , $level );
		$this->addMsg ( "playlist" , $wrapper ) ;
	}
}
?>