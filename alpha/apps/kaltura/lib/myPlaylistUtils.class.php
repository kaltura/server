<?php
/**
 * Will encapsulate functions for handling playlists
 */
class myPlaylistUtils
{
	// change the total results to 30 for performance reasons
	const TOTAL_RESULTS = 50;//100;
	
	private static $user_cache = null;
	
	private static $isAdminKs = false;
	
	public static function setIsAdminKs($v)
	{
		self::$isAdminKs = $v;
	}
	
	private static $attachCriteriaHandler = null;
	
	/**
	 * input - $obj is object that implements IKalturaPlaylistUtils
	 * @return void
	 */
	public static function setAttachCriteriaHandler($obj)
	{
		self::$attachCriteriaHandler = $obj;
	}
/**
 * Playlist is an entry of type ENTRY_TYPE_PLAYLIST = 5.
 * Within this type there are 3 media_types to tell the difference between dynamic,static and external playslits:
 * dynamic 	media_type = ENTRY_MEDIA_TYPE_XML = 10
 * static 	media_type = ENTRY_MEDIA_TYPE_TEXT = 3
 * external media_type = ENTRY_MEDIA_TYPE_GENERIC_1= 101;	// these types can be used for derived classes - assume this is some kind of TXT file
 *
 * 
 */	
	public static function validatePlaylist ( $playlist )
	{
		if ( ! $playlist )	 throw new Exception ( "No playlist to validate" );
		if ( $playlist->getMediaType() == entry::ENTRY_MEDIA_TYPE_TEXT )
		{
			// assume this is a static playlist
			$static_playlist_str = $playlist->getDataContent(true);
			$static_playlist = explode ( "," , $static_playlist_str );
			$fixed_playlist = array();
//			$entry_id = "";
			foreach ( $static_playlist as &$entry_id ) 
			{
				// TODO - hack for removing 'null' from the entry id due to a bug on the client's side
				$trimmed = preg_replace ( "/null/" , "" , trim ( $entry_id ) );
				if ( $trimmed ) { $fixed_playlist[] = $trimmed; }
			}
		
			$fixed_playlist_str = implode ( "," , $fixed_playlist );
			$playlist->setDataContent( $fixed_playlist_str , false ); // don't increment the version after fixing the data
		}
		elseif ( $playlist->getMediaType() == entry::ENTRY_MEDIA_TYPE_XML )
		{
			// assume this is a dynamic playlist			
			// TODO - validate XML
			$dynamic_playlist_str = $playlist->getDataContent(true);
			KalturaLog::log( "Playlist [" . $playlist->getId() . "] [" . $playlist->getName() . "] dataContent:\n" . $dynamic_playlist_str );
			if ( ! $dynamic_playlist_str ) $playlist->setDataContent( null , false ); // set to null and it the content of the xml won't be update
		}
		elseif ( $playlist->getMediaType() == entry::ENTRY_MEDIA_TYPE_GENERIC_1 )
		{
			// assume this is an external playlist			
		}
		else
		{
			throw new Exception ( 'Invalid play list type' );
		}
		
		
	}
	
	// will update the statistics of the playlist:
	// count - the number of entries that return at the current time
	// countDate - now() - so will be able to expire the statistics
	// lenghtInMsecs - the calculated duration (assuming the image take 0 seconds )
	public static function updatePlaylistStatistics ( $partner_id , $playlist )
	{
		$entry_list = self::executePlaylist( $partner_id , $playlist );
		$count = count ( $entry_list );
		$count_date = time();
		$duration = 0;
		if ( $count > 0 )
		{
			$duration = 0;
			foreach ( $entry_list  as $entry )
			{
				$duration += $entry->getLengthInMsecs();
			}
		}
		$playlist->setCount ( $count );
		$playlist->setCountDate ( $count_date );
		$playlist->setLengthInMsecs ( $duration );
		return  $entry_list;
	}
	
	/**
	 * a playlist is an entry (type=ENTRY_TYPE_PLAYLIST and media_type=ENTRY_MEDIA_TYPE_XML).
	 * The XML file will hold a filter list 
	 * 	total number of results
	 *  n x entryFilter (allowing only spesific clips) - limited to a given number of results
	 * when executing the playlist, each entryFilter will be used to retrieve the number of entries (limited to a system defined max-limit)
	 * if after the entryFilter retrieved a list, still not enough entries (less than list's total number of results) - go to next list
	 * 
	 */
	public static function executePlaylistById ( $partner_id , $playlist_id ,  $extra_filters = null , $detailed = true )
	{
		$playlist = entryPeer::retrieveByPK( $playlist_id );

		if ( ! $playlist )
		{
			throw new Exception( "Invalid entry id [$playlist_id]" ) ; 
		}
		
		if ( $playlist->getType() != entry::ENTRY_TYPE_PLAYLIST )
		{
			throw new Exception( "Invalid entry id [$playlist_id]" ) ;
		}
		
		// the default of detrailed should be true - most of the time the kuse is needed 
		if ( is_null ( $detailed ) ) $detailed = true ; 
		return self::executePlaylist ( $partner_id , $playlist ,  $extra_filters , $detailed );
	}
	
	public static function executePlaylist ( $partner_id , $playlist ,  $extra_filters = null , $detailed = true )
	{
		if ( ! $playlist )
		{
			throw new Exception( "Invalid entry id" ) ;
		}
		 
		// the default of detrailed should be true - most of the time the kuse is needed 
		if ( is_null ( $detailed ) ) $detailed = true ; 
		
		if ( $playlist->getMediaType() == entry::ENTRY_MEDIA_TYPE_XML )
		{
			// dynamix playlist
		 	// the content is a valid xml that hods the list
			$filter_list_content = $playlist->getDataContent();
			if ( ! $filter_list_content )
			{
				$filter_list_content = $playlist->getDataContent( true );
			}
			
			return self::executeDynamicPlaylist ( $partner_id ,  $filter_list_content , $extra_filters , $detailed );
		}
		elseif ( $playlist->getMediaType() == entry::ENTRY_MEDIA_TYPE_GENERIC_1 )
		{
			// assume this is an external playlist			
			// TODO - validate XML
		}
		else
		{
			// static playlist
			// search the roughcut_entry for the playlist as a roughcut / group
			return self::executeStaticPlaylist ( $playlist , $extra_filters , $detailed );
		}
	}
	
	public static function getPlaylistFiltersById($playlist_id)
	{
		$playlist = entryPeer::retrieveByPK( $playlist_id );

		if ( ! $playlist )
		{
			throw new Exception( "Invalid entry id [$playlist_id]" ) ; 
		}
		
		if ( $playlist->getType() != entry::ENTRY_TYPE_PLAYLIST )
		{
			throw new Exception( "Invalid entry id [$playlist_id]" ) ;
		}
		
		return self::getPlaylistFilters ( $playlist );
	}
	
	public static function getPlaylistFilters(entry $playlist)
	{
		if($playlist->getMediaType() == entry::ENTRY_MEDIA_TYPE_XML)
		{
			$xml = $playlist->getDataContent();
			if(!$xml)
				$xml = $playlist->getDataContent(true);
			
			return self::getDynamicPlaylistFilters($xml);
		}
		else
		{
			return self::getStaticPlaylistFilters($playlist);
		}
	}
	
	public static function getStaticPlaylistFilters(entry $playlist)
	{
		$entriesList = explode(',', $playlist->getDataContent());
		$filter = new entryFilter();
		$filter->setIdIn($entriesList);
		
		return array($filter);
	}
	
	public static function executeStaticPlaylist ( entry $playlist , $extra_filters  = null, $detailed = true )
	{
		$entry_id_list_str = $playlist->getDataContent();
		return self::executeStaticPlaylistFromEntryIdsString($entry_id_list_str, $extra_filters, $detailed);
	}
	
	public static function executeStaticPlaylistFromEntryIdsString($entry_id_list_str, $extra_filters = null, $detailed = true)
	{
		$entry_id_list = explode ( "," , $entry_id_list_str );
		if ( $entry_id_list )
		{
			// clear white spaces - TODO - assume this is done at insert time
			foreach ( $entry_id_list as &$entry_id ) 
				$entry_id=trim($entry_id);
		}
		else
		{
			return null;//array();
		}
		
		return self::executeStaticPlaylistFromEntryIds($entry_id_list, $extra_filters, $detailed);
	}
	
	public static function executeStaticPlaylistFromEntryIds(array $entry_id_list, $extra_filters = null, $detailed = true)
	{
		// if exists extra_filters - use the first one to filter the entry_id_list
		$c= KalturaCriteria::create(entryPeer::OM_CLASS);
		$c->add ( entryPeer::ID , $entry_id_list , Criteria::IN ); 
		
		if (!self::$isAdminKs)
		{
			self::addSchedulingToCriteria($c);
		}
		
		self::addModerationToCriteria($c);
		
		if ( $extra_filters && isset ( $extra_filters[1] )  )
		{
			// use index 1 - will be used as the first filter
			$entry_filter = $extra_filters[1];
			
			// read the _eq_display_in_search field but ignore it because it's part of a more complex criterion - see bellow
			$display_in_search = $entry_filter->get( "_eq_display_in_search");
			if ( $display_in_search >= 2 )
			{
				$entry_filter->set ( "_eq_display_in_search" , null );
			}
			
			$entry_filter->attachToCriteria( $c );
			
			if(self::$attachCriteriaHandler != null)
			{
				self::$attachCriteriaHandler->attachCriteriaHandler($c);
			}
			
			// add some hard-coded criteria
			$c->addAnd ( entryPeer::TYPE , array ( entry::ENTRY_TYPE_MEDIACLIP , entry::ENTRY_TYPE_SHOW ) , Criteria::IN ); // search only for clips or roughcuts
			$c->addAnd ( entryPeer::STATUS , entry::ENTRY_STATUS_READY ); // search only for READY entries 

			if ( $display_in_search >= 2 )
			{
				// We don't allow searching in the KalturaNEtwork anymore (mainly for performance reasons)
				// allow only assets for the partner  
				$c->addAnd ( entryPeer::PARTNER_ID , $partner_id ); // 
/*				
				$crit = $c->getNewCriterion ( entryPeer::PARTNER_ID , $partner_id );
				$crit->addOr ( $c->getNewCriterion ( entryPeer::DISPLAY_IN_SEARCH , $display_in_search ) );
				$c->addAnd ( $crit );
*/
			}
		}

		if ( $detailed )
			$unsorted_entry_list = entryPeer::doSelectJoinkuser( $c ); // maybe join with kuser to add some data about the contributor
		else
			$unsorted_entry_list = entryPeer::doSelect( $c ); // maybe join with kuser to add some data about the contributor
	
		// now sort the list according to $entry_id_list
		
		$entry_list = array();
		// build a map where the key is the id of the entry
		$id_list = self::buildIdMap( $unsorted_entry_list );

		// VERY STRANGE !! &$entry_id must be with a & or else the values of the array change !!!
		foreach ( $entry_id_list as &$entry_id )
		{
			if ( $entry_id != "" )
			{
				// allow only ready entries
				$current_entry = @$id_list[$entry_id];
				if ( $current_entry && $current_entry->getStatus() == entry::ENTRY_STATUS_READY )
				{
					// add to the entry_list only when the entry_id is not empty 
					$entry_list[] = $current_entry;
				} 
			}
		}
		if ( count( $entry_list ) == 0 ) return null;

		return $entry_list;
	}
	private static function buildIdMap ( $list )
	{
		if( ! $list ) return null;
		$ids = array ();
		foreach ( $list  as $elem )
		{
			$ids[$elem->getId()] = $elem;
		}
		return $ids;
	}
	
// TODO - create a schema for the xml 
		
/**
 * <playlist>
	<total_results>6</total_results>
	<filters>
		<filter>
			<limit>3</limit>
			<mlikeor_tags>dog football</mlikeor_tags>
			<gte_created_at></gte_created_at>
			<lte_created_at></lte_created_at>
			<in_media_type>1,5</in_media_type>
			<order_by>-created_at</order_by>
		</filter>
		<filter>
			<limit>7</limit>
			<like_tags>cat</like_tags>
			<gte_created_at></gte_created_at>
			<lte_created_at></lte_created_at>
			<in_media_type>1,5</in_media_type>
		</filter>
	</filters>
</playlist>
 
 */	
	public static function getDynamicPlaylistFilters($xml)
	{
		list ( $total_results , $list_of_filters ) = self::getPlaylistFilterListStruct ( $xml );
		if ( ! $list_of_filters ) 
			return array();
	
		$entry_filters = array();
		foreach ( $list_of_filters as $entry_filter_xml )
		{
			$entry_filter = new entryFilter();
			$entry_filter->fillObjectFromXml( $entry_filter_xml , "_" ); 
			
			$entry_filters[] = $entry_filter;
		}
		return $entry_filters;
	}
	
	public static function executeDynamicPlaylist ( $partner_id , $xml , $extra_filters = null ,$detailed = true )
	{
		list ( $total_results , $list_of_filters ) = self::getPlaylistFilterListStruct ( $xml );
	
		$entry_filters = array();

		if ( ! $list_of_filters ) return null;
		// TODO - for now we assume that there are more or equal filters in the XML than the ones from the request
		$i = 1; // the extra_filter is 1-based
		foreach ( $list_of_filters as $entry_filter_xml )
		{
			// 	in general this service can fetch entries from kaltura networks.
			// for each filter we should decide if thie assumption is true...
			$allow_partner_only = true;
			
			// compile all the filters - only then execute them if not yet reached the total_results
			// TODO - optimize - maybe create them only when needed. - For now it's safer to compile all even if not needed.
			$entry_filter = new entryFilter();
			// add the desired prefix "_" because the XML is not expected to have it while the entryFilter class expects it
			$entry_filter->fillObjectFromXml( $entry_filter_xml , "_" ); 
			// make sure there is alway a limit for each filter - if not an explicit one - the system limit should be used
			if( $entry_filter->getLimit() == null || $entry_filter->getLimit() < 1 )
			{
				$entry_filter->setLimit( self::TOTAL_RESULTS );
			}
			
			$extra_filter = @$extra_filters[$i];
			// merge the current_filter with the correcponding extra_filter
			// allow the extra_filter to override properties of the current filter

			if ( $extra_filter )
			{
				$entry_filter->fillObjectFromObject( $extra_filter , 
					myBaseObject::CLONE_FIELD_POLICY_THIS , 
					myBaseObject::CLONE_POLICY_PREFER_NEW , null , null , false );
					
				$entry_filter->setPartnerSearchScope ( baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE );
			}
			
			self::updateEntryFilter( $entry_filter ,  $partner_id , true );
			
			$entry_filters[] = $entry_filter;

			$i++;	
		}
		
		$number_of_entries = 0;
		$entry_list = array();
		$i = 1;		
		foreach ( $entry_filters as $entry_filter )
		{
			$current_limit = max ( 0 , $total_results - $number_of_entries ); // if the current_limit is < 0 - set it to be 0
			$exclude_id_list = self::getIds( $entry_list );
			$c = KalturaCriteria::create("entry");
			
			
			// don't fetch the same entries twice - filter out all the entries that were already fetched
			if( $exclude_id_list ) $c->add ( entryPeer::ID , $exclude_id_list , Criteria::NOT_IN );  
			
			// no need to fetch any more results
			if ( $current_limit <= 0  )break;
			
			$filter_limit = $entry_filter->getLimit ();
			
			if ( $filter_limit > $current_limit )
			{
				// set a smaller limit incase the filter's limit is to high
				$entry_filter->setLimit ( $current_limit );
			}

			// read the _eq_display_in_search field but ignore it because it's part of a more complex criterion
			$display_in_search = $entry_filter->get( "_eq_display_in_search");
			if ( $display_in_search >= 2 )
			{
				$entry_filter->set ( "_eq_display_in_search" , null );
			}
			
			entryFilter::forceMatch ( true ); // use the MATCH mechanism
			$entry_filter->attachToCriteria( $c );

			if(self::$attachCriteriaHandler != null)
			{
				self::$attachCriteriaHandler->attachCriteriaHandler($c);
			}
			
			// add some hard-coded criteria
			$c->addAnd ( entryPeer::TYPE , array ( entry::ENTRY_TYPE_MEDIACLIP , entry::ENTRY_TYPE_SHOW , entry::ENTRY_TYPE_LIVE_STREAM ) , Criteria::IN ); // search only for clips or roughcuts
			$c->addAnd ( entryPeer::STATUS , entry::ENTRY_STATUS_READY ); // search only for READY entries 

			if ( $display_in_search >= 2 )
			{
				// We don't allow searching in the KalturaNEtwork anymore (mainly for performance reasons)
				// allow only assets for the partner  
				$c->addAnd ( entryPeer::PARTNER_ID , $partner_id ); // 
/*				
				$crit = $c->getNewCriterion ( entryPeer::PARTNER_ID , $partner_id );
				$crit->addOr ( $c->getNewCriterion ( entryPeer::DISPLAY_IN_SEARCH , $display_in_search ) );
				$c->addAnd ( $crit );
*/
			}
			
			if (!self::$isAdminKs)
			{
				self::addSchedulingToCriteria($c);
			}
			
			self::addModerationToCriteria($c);
			
			if ( $detailed )
				$entry_list_for_filter = entryPeer::doSelectJoinkuser( $c ); // maybe join with kuser to add some data about the contributor
			else
				$entry_list_for_filter = entryPeer::doSelect( $c ); // maybe join with kuser to add some data about the contributor
			
			// update total count and merge current result with the global list
			$number_of_entries += count ( $entry_list_for_filter );
			$entry_list = array_merge ( $entry_list , $entry_list_for_filter );
		}
		
		return $entry_list;		 
	}
	
	// will assume that user_id is actually the puser_id and should be replaced by kuser_id to be able to search by in the entry table
	private static function setUser ( $partner_id , $filter )
	{
		$target_puser_id = $filter->get ( "_eq_user_id" );
		if ( $target_puser_id !== null )
		{
			$puser_kuser = self::getPuserKuserFromCache ( $target_puser_id );
			if( $puser_kuser == null )
			{
				$puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid( $partner_id , null /* $subp_id */, $target_puser_id , false);
				if ( $puser_kuser )
				{
					$filter->set ( "_eq_user_id" ,  $puser_kuser->getkuserId() );
				}
				self::setPuserKuserFromCache( $target_puser_id , $puser_kuser );
			}
		}		
	}
	
	private static function getPuserKuserFromCache ( $puser_id )
	{
		if ( self::$user_cache == null ) return null;
		{
			return  @self::$user_cache[$puser_id] ;
		} 
	}

	private static  function setPuserKuserFromCache ( $puser_id , $puser_kuser )
	{
		if ( self::$user_cache == null ) self::$user_cache = array();
		self::$user_cache[$puser_id] = $puser_kuser; 
	}
	
	public static function getPlaylistFilterListStruct ( $xml )
	{
		try
		{
			@$simple_xml = new SimpleXMLElement( $xml );
//print_r ( $simple_xml );			
			$total_results_node = $simple_xml->xpath ( "total_results" );
			$total_result = self::TOTAL_RESULTS;
			if ( $total_results_node  )
			{ 
				if ( is_array ( $total_results_node ) )
				{
					if( count ( $total_results_node ) > 1 ) throw new Exception ( "Must not have more than 1 element of 'total_results'");
//print_r ( $total_results_node)	;				
					$total_result = $total_results_node[0]; 				
				}
			}	

			// TODO - stick to the first option and change all the <filter> objects to be children of <filters>  
			$list_of_filters = $total_results_node = $simple_xml->xpath ( "filters/filter" );
			if ( ! $list_of_filters )
				$list_of_filters = $total_results_node = $simple_xml->xpath ( "filter" );
			if ( $total_result > self::TOTAL_RESULTS ) $total_result = self::TOTAL_RESULTS; // don't let anyone exceed the system's TOTAL_RESULT
			return array ( $total_result , $list_of_filters );
		}
		catch ( Exception $ex )
		{
			
		}		
	}
	
	public static function getEmbedCode ( entry $playlist , $wid , $ui_conf_id , $uid = null , $autoplay = null )
	{
		if ( $playlist == null ) return "";
		
		if ( ! $uid ) $uid = "0";
		
		$partner_id = $playlist->getPartnerId();
		$subp_id  = $playlist->getSubpId();
		$partner= PartnerPeer::retrieveByPK( $partner_id );
		
		$host = myPartnerUtils::getHost($partner_id);
		
		$playlist_flashvars = self::toPlaylistUrl ( $playlist , $host ) ;
		
		if ( $wid == null ) $wid = $partner->getDefaultWidgetId();
		$widget = widgetPeer::retrieveByPK( $wid );
		
		// use the ui_conf from the widget only if it was not explicitly set 
		if ( $ui_conf_id == null )	$ui_conf_id = $widget->getUiConfId(); 
		$ui_conf = uiConfPeer::retrieveByPK( $ui_conf_id );
		
		if ( ! $ui_conf ) 
		{
			throw new Exception( "Invalid uiconf id [$ui_conf_id] for widget [$wid]" ) ;
		}
		
//		$autoplay_str = $autoplay ? "autoPlay=true" : "autoPlay=false" ; 
		$autoplay_str = "";
$embed = <<< HTML
<object height="{$ui_conf->getHeight()}" width="{$ui_conf->getWidth()}" type="application/x-shockwave-flash" data="{$host}/kwidget/wid/{$wid}/ui_conf_id/{$ui_conf_id}" id="kaltura_playlist" style="visibility: visible;">		
<param name="allowscriptaccess" value="always"/><param name="allownetworking" value="all"/><param name="bgcolor" value="#000000"/><param name="wmode" value="opaque"/><param name="allowfullscreen" value="true"/>
<param name="movie" value="{$host}/kwidget/wid/{$wid}/ui_conf_id/{$ui_conf_id}"/>
<param name="flashvars" value="layoutId=playlistLight&uid={$uid}&partner_id={$partner_id}&subp_id={$subp_id}&$playlist_flashvars"/></object>
HTML;
		return array ( $embed , $ui_conf->getWidth() ,  $ui_conf->getHeight() ); 		
	}
	
	public static function toPlaylistUrl ( entry $playlist , $host  , $uid = null )
	{
		$partner_id = $playlist->getPartnerId();
		$subp_id  = $playlist->getSubpId();			
		
		if ( $playlist->getMediaType() == entry::ENTRY_MEDIA_TYPE_GENERIC_1 ) 
		{
			$playlist_url = urlencode ( $playlist->getDataContent() ); // when of type GENERIC === MRSS -> the data content is the url to point to 
		}
		else
		{
			$playlist_url = urlencode ( $host . "/index.php/partnerservices2/executeplaylist?" .
				"uid={$uid}&partner_id={$partner_id}&subp_id={$subp_id}&format=8&" .   // make sure the format is 8 - mRss
				"ks={ks}&" .   
				self::toQueryString( $playlist , false ) ); 
		}
		
//		$str = "k_pl_autoContinue=true&k_pl_autoInsertMedia=true&k_pl_0_name=" . $playlist->getName() . "&k_pl_0_url=" . $playlist_url;
		$str = "k_pl_0_name=" . $playlist->getName() . "&k_pl_0_url=" . $playlist_url;
		return $str;
	}
	
	public static function getExecutionUrl ( entry $playlist )
	{
		if ( ! $playlist ) return "";
		if ( $playlist->getMediaType() == entry::ENTRY_MEDIA_TYPE_GENERIC_1 )
		{
			return $playlist->getDataContent(); 
		}
		
		$host = requestUtils::getRequestHost();
		$playlist_url = 
			$host . "/index.php/partnerservices2/executeplaylist?format=8&" .
//				"uid={uid}&partner_id={partnerid}&subp_id={subpid}&" .   // make sure the format is 8 - mRss
				"ks={ks}&" .   
				self::toQueryString( $playlist , false ) ; 		
		return 	$playlist_url;			
	}
	
	// for now - don't appen dht efilter to the url
	public static function toQueryString ( entry $playlist ,$should_append_filter_to_url = false )
	{
		$query = "playlist_id={$playlist->getId()}";
		
		if ( $playlist->getMediaType() != entry::ENTRY_MEDIA_TYPE_XML )
			return $query;
			
		if ( !$should_append_filter_to_url ) return $query;
		 
		$xml = $playlist->getDataContent();
		list ( $total_results , $list_of_filters ) = self::getPlaylistFilterListStruct ( $xml );
		
		$entry_filters = array();
		$partner_id = $playlist->getPartnerId(); 
		
		// add ks=_KS_ for the playlist to replace it before hitting the executePlaylist 
		$query .= "&fp=f"; // make sure the filter prefix is short
		
		if ( ! $list_of_filters ) return $query;
		
		$i = 1; // the extra_filter is 1-based
		foreach ( $list_of_filters as $entry_filter_xml )
		{
			$prefix = "f{$i}_";
			// 	in general this service can fetch entries from kaltura networks.
			// for each filter we should decide if thie assumption is true...
			$allow_partner_only = true;
			
			// compile all the filters - only then execute them if not yet reached the total_results
			// TODO - optimize - maybe create them only when needed. - For now it's safer to compile all even if not needed.
			$entry_filter = new entryFilter();
			// add the desired prefix "_" because the XML is not expected to have it while the entryFilter class expects it
			$entry_filter->fillObjectFromXml( $entry_filter_xml , "_" ); 
			// make sure there is alway a limit for each filter - if not an explicit one - the system limit should be used
			if( $entry_filter->getLimit() == null || $entry_filter->getLimit() < 1 )
			{
				$entry_filter->setLimit( self::TOTAL_RESULTS );
			}
			
			$entry_filter->setPartnerSearchScope ( baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE );
			self::updateEntryFilter( $entry_filter ,  $partner_id );

			//$entry_filters[] = $entry_filter;
			$fields = $entry_filter->fields;
			foreach ( $fields as $field => $value )
			{
				if ( $value )
				$query .= "&" . $prefix . $field . "=" . $value;				
			}
			$i++;	
		}
		return $query;
	}
	
	// will update the entry filter according to the partner_id, $use_filter_puser_id and some of the attributes in the entry_filter
	private static function updateEntryFilter(entryFilter $entry_filter, $partner_id)
	{
		self::setUser ( $partner_id , $entry_filter );
		
		$display_in_search = $entry_filter->getDisplayInSearchEquel();
	
		// 2009-07-12, Liron: changed the detfault - prferer partner only unless explicitly defined $display_in_search=2;
		$allow_partner_only = ( $display_in_search === null || $display_in_search < 2 );
		if ( $allow_partner_only ) 
		{
			$entry_filter->setPartnerIdEquel($partner_id);
			$entry_filter->setPartnerSearchScope($partner_id);
		}	
		else
		{
			$entry_filter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		}
	}
	
	
	private static function getIds ( $list )
	{
		$id_list  =array();
		foreach ( $list as $elem )
		{
			$id_list[] = $elem->getId(); 
		}
		
		return $id_list;
	}
	
	private static function addSchedulingToCriteria(Criteria $c)
	{
		$startDateCriterion = $c->getNewCriterion(entryPeer::START_DATE, time(), Criteria::LESS_EQUAL);
		$startDateCriterion->addOr($c->getNewCriterion(entryPeer::START_DATE, null));
		
		$endDateCriterion = $c->getNewCriterion(entryPeer::END_DATE, time(), Criteria::GREATER_EQUAL);
		$endDateCriterion->addOr($c->getNewCriterion(entryPeer::END_DATE, null));
		
		$c->addAnd($startDateCriterion);
		$c->addAnd($endDateCriterion);
	}
	
	private static function addModerationToCriteria(Criteria $c)
	{
		// add moderation status not pending moderation or rejected
		$moderationStatusesNotIn = array(
			entry::ENTRY_MODERATION_STATUS_PENDING_MODERATION, 
			entry::ENTRY_MODERATION_STATUS_REJECTED);
		$c->add(entryPeer::MODERATION_STATUS, $moderationStatusesNotIn, Criteria::NOT_IN);
	}
	
	/**
	 * @return bool
	 */
	public static function isEntryInPlaylist($entryId, $playlistId, $partnerId)
	{
		$playlistEntry = entryPeer::retrieveByPK($playlistId);
		if(!$playlistEntry) return false;
		
		if($playlistEntry->getMediaType() == entry::ENTRY_MEDIA_TYPE_TEXT)
		{
			// assume static playlist
			$static_playlist_str = $playlistEntry->getDataContent();
			$static_playlist = explode ( "," , $static_playlist_str );
			if(in_array($entryId, $static_playlist)) return true;
		}
		/*
		elseif ( $playlistEntry->getMediaType() == entry::ENTRY_MEDIA_TYPE_XML )
		{
			$entries = self::executeDynamicPlaylist($partnerId, $playlistEntry->getDataContent());
			$entryIds = self::getIds($entries);
			if(in_array($entryId, $entryIds)) return true;
		}*/
		return false;
	}
}

interface IKalturaPlaylistUtils
{
	public function attachCriteriaHandler(Criteria &$c);
}
?>