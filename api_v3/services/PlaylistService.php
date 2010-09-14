<?php
/**
 * Playlist service lets you create,manage and play your playlists
 * Playlists could be static (containing a fixed list of entries) or dynamic (baseed on a filter)
 *
 * @service playlist
 *
 * @package api
 * @subpackage services
 */
class PlaylistService extends KalturaEntryService
{
	public function initService ($partner_id , $puser_id , $ks_str , $service_name , $action )
	{
		parent::initService ($partner_id , $puser_id , $ks_str , $service_name , $action );
		parent::applyPartnerFilterForClassNoKalturaNetwork ( new accessControlPeer() );
	}
	
	/**
	 * Add new playlist
	 * Note that all entries used in a playlist will become public and may appear in KalturaNetwork
	 * 
	 * @action add
	 * @param KalturaPlaylist $playlist 
	 * @param bool $updateStats 
	 * @return KalturaPlaylist
	 */
	function addAction( KalturaPlaylist $playlist , $updateStats = false)
	{
		$dbPlaylist = $playlist->toPlaylist();
		
		$this->checkAndSetValidUser($playlist, $dbPlaylist);
		$this->checkAdminOnlyInsertProperties($playlist);
		$this->validateAccessControlId($playlist);
		$this->validateEntryScheduleDates($playlist);
		
		$dbPlaylist->setPartnerId ( $this->getPartnerId() );
		$dbPlaylist->setStatus ( entry::ENTRY_STATUS_READY );
		$dbPlaylist->setKshowId ( null ); // this is brave !!
		$dbPlaylist->setType ( entry::ENTRY_TYPE_PLAYLIST );
		
		myPlaylistUtils::validatePlaylist( $dbPlaylist );
		
		$dbPlaylist->save();
		
		if ( $updateStats ) 
			myPlaylistUtils::updatePlaylistStatistics( $dbPlaylist->getPartnerId() , $dbPlaylist );
		$dbPlaylist->setDisplayInSearch ( 2 ); // make all the playlist entries PUBLIC !
		
		$playlist = new KalturaPlaylist(); // start from blank
		$playlist->fromPlaylist( $dbPlaylist );
		
		return $playlist;
	}
	

	/**
	 * Retrieve a playlist
	 * 
	 * @action get
	 * @param string $id 
	 * @param int $version Desired version of the data
	 * @return KalturaPlaylist
	 *
	 * @throws APIErrors::INVALID_ENTRY_ID
	 * @throws APIErrors::INVALID_PLAYLIST_TYPE
	 */
	function getAction( $id, $version = -1 )
	{
		$dbPlaylist = entryPeer::retrieveByPK( $id );
		
		if ( ! $dbPlaylist )
			throw new KalturaAPIException ( APIErrors::INVALID_ENTRY_ID , "Playlist" , $id  );
		if ( $dbPlaylist->getType() != entry::ENTRY_TYPE_PLAYLIST )
			throw new KalturaAPIException ( APIErrors::INVALID_PLAYLIST_TYPE );
			
		if ($version !== -1)
			$dbPlaylist->setDesiredVersion($version);
			
		$playlist = new KalturaPlaylist(); // start from blank
		$playlist->fromPlaylist( $dbPlaylist );
		
		return $playlist;
	}
		
	/**
	 * Update existing playlist
	 * Note - you cannot change playlist type. updated playlist must be of the same type.
	 * 
	 * @action update
	 * @param string $id 
	 * @param KalturaPlaylist $playlist
	 * @param bool $updateStats  
	 * @return KalturaPlaylist
	 *
	 * @throws APIErrors::INVALID_ENTRY_ID
	 * @throws APIErrors::INVALID_PLAYLIST_TYPE
	 */	
	function updateAction( $id , KalturaPlaylist $playlist , $updateStats = false )
	{
		$dbPlaylist = entryPeer::retrieveByPK( $id );
		
		if ( ! $dbPlaylist )
			throw new KalturaAPIException ( APIErrors::INVALID_ENTRY_ID , "Playlist" , $id  );
		if ( $dbPlaylist->getType() != entry::ENTRY_TYPE_PLAYLIST )
			throw new KalturaAPIException ( APIErrors::INVALID_PLAYLIST_TYPE );
		
		$playlist->playlistType = $dbPlaylist->getMediaType();
		$playlistUpdate = $playlist->toUpdatablePlaylist();

		
		$this->checkIfUserAllowedToUpdateEntry($playlistUpdate);
		$this->checkAndSetValidUser($playlist, $playlistUpdate);
		$this->checkAdminOnlyUpdateProperties($playlist);
		$this->validateAccessControlId($playlist);
		$this->validateEntryScheduleDates($playlist);

		$allowEmpty = true ; // TODO - what is the policy  ? 
		if ( $playlistUpdate->getMediaType() && ($playlistUpdate->getMediaType() != $dbPlaylist->getMediaType() ) )
		{
			throw new KalturaAPIException ( APIErrors::INVALID_PLAYLIST_TYPE );
		}
		else
		{
			$playlistUpdate->setMediaType( $dbPlaylist->getMediaType() ); // incase  $playlistUpdate->getMediaType() was empty
		}

		// copy properties from the playlistUpdate to the $dbPlaylist
		baseObjectUtils::autoFillObjectFromObject ( $playlistUpdate , $dbPlaylist , $allowEmpty );
		// after filling the $dbPlaylist from  $playlist - make sure the data content is set properly
		if ( $playlistUpdate->getDataContent(true) ) $dbPlaylist->setDataContent ( $playlistUpdate->getDataContent(true)  );

		myPlaylistUtils::validatePlaylist( $dbPlaylist );
		
		if ( $updateStats )
			myPlaylistUtils::updatePlaylistStatistics ( $this->getPartnerId() , $dbPlaylist );//, $extra_filters , $detailed );
		
		$dbPlaylist->save();
		$playlist->fromPlaylist( $dbPlaylist );
		
		return $playlist;
	}	
		

	/**
	 * Delete existing playlist
	 * 
	 * @action delete
	 * @param string $id 
	 *
	 * @throws APIErrors::INVALID_ENTRY_ID
	 * @throws APIErrors::INVALID_PLAYLIST_TYPE
	 */		
	function deleteAction(  $id )
	{
		$this->deleteEntry($id, KalturaEntryType::PLAYLIST);
	}	
	
	/**
	 * List available playlists
	 * 
	 * @action list
	 * @param KalturaPlaylistFilter // TODO 
	 * @param KalturaFilterPager $pager
	 * @return KalturaPlaylistListResponse
	 */
	function listAction( KalturaPlaylistFilter $filter=null, KalturaFilterPager $pager=null )
	{
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		

	    if (!$filter)
			$filter = new KalturaPlaylistFilter();
			
	    $filter->typeEqual = KalturaEntryType::PLAYLIST;
	    list($list, $totalCount) = parent::listEntriesByFilter($filter, $pager, baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
	    
	    $newList = KalturaPlaylistArray::fromPlaylistArray($list);
		$response = new KalturaPlaylistListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;	
	}
	
	/**
	 * Retrieve playlist for playing purpose
	 * 
	 * @action execute
	 * @param string $id 
	 * @param string $detailed
	 * @return KalturaBaseEntryArray
	 */
	function executeAction( $id , $detailed = false )
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

		$extraFilters = array();
		
		if ($this->getKs() && is_object($this->getKs()) && $this->getKs()->isAdmin())
			myPlaylistUtils::setIsAdminKs(true);
			
		$entryList= myPlaylistUtils::executePlaylistById( $this->getPartnerId() , $id , $extraFilters , $detailed );
		myEntryUtils::updatePuserIdsForEntries ( $entryList );
		
		return KalturaBaseEntryArray::fromEntryArray( $entryList );		
	}

	/**
	 * Retrieve playlist for playing purpose, based on content
	 * 
	 * @action executeFromContent
	 * @param KalturaPlaylistType $playlistType  
	 * @param string $playlistContent
	 * @param string $detailed
	 * @return KalturaBaseEntryArray
	 */
	function executeFromContentAction($playlistType, $playlistContent, $detailed = false)
	{
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
	    
		if ($this->getKs() && is_object($this->getKs()) && $this->getKs()->isAdmin())
			myPlaylistUtils::setIsAdminKs(true);
			
		$entryList = array();
		if ($playlistType == KalturaPlaylistType::DYNAMIC)
			$entryList = myPlaylistUtils::executeDynamicPlaylist($this->getPartnerId(), $playlistContent);
		else if ($playlistType == KalturaPlaylistType::STATIC_LIST)
			$entryList = myPlaylistUtils::executeStaticPlaylistFromEntryIdsString($playlistContent);
			
		myEntryUtils::updatePuserIdsForEntries($entryList);
		
		return KalturaBaseEntryArray::fromEntryArray($entryList);
	}
	
	/**
	 * Revrieve playlist for playing purpose, based on media entry filters
	 * 
	 * @action executeFromFilters
	 * @param KalturaMediaEntryFilterForPlaylistArray $filters
	 * @param int $totalResults
	 * @param string $detailed
	 * @return KalturaBaseEntryArray
	 */
	function executeFromFiltersAction(KalturaMediaEntryFilterForPlaylistArray $filters, $totalResults, $detailed = false)
	{
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
	    
		$tempPlaylist = new KalturaPlaylist();
		$tempPlaylist->playlistType = KalturaPlaylistType::DYNAMIC;
		$tempPlaylist->filters = $filters;
		$tempPlaylist->totalResults = $totalResults;
		$tempPlaylist->filtersToPlaylistContentXml();
		return $this->executeFromContentAction($tempPlaylist->playlistType, $tempPlaylist->playlistContent, true); 
	}
	
	
	/**
	 * Retrieve playlist statistics
	 * 
	 * @action getStatsFromContent
	 * @param KalturaPlaylistType $playlistType  
	 * @param string $playlistContent
	 * @return KalturaPlaylist
	 */
	function getStatsFromContentAction( $playlistType , $playlistContent )
	{
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
	    
		$dbPlaylist = new entry();
		$dbPlaylist->setId( -1 ); // set with some dummy number so the getDataContent will later work properly 
		$dbPlaylist->setType ( entry::ENTRY_TYPE_PLAYLIST ); // prepare the playlist type before filling from request
		$dbPlaylist->setMediaType ( $playlistType );
		$dbPlaylist->setDataContent( $playlistContent );
				
		myPlaylistUtils::updatePlaylistStatistics ( $this->getPartnerId() , $dbPlaylist );//, $extra_filters , $detailed );
		
		$playlist = new KalturaPlaylist(); // start from blank
		$playlist->fromPlaylist( $dbPlaylist );
		
		return $playlist;
	}
	

}
