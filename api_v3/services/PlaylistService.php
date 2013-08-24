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
	/* (non-PHPdoc)
	 * @see KalturaBaseService::globalPartnerAllowed()
	 */
	protected function kalturaNetworkAllowed($actionName)
	{
		if ($actionName === 'executeFromContent') {
			return true;
		}
		if ($actionName === 'executeFromFilters') {
			return true;
		}
		if ($actionName === 'getStatsFromContent') {
			return true;
		}
		return parent::kalturaNetworkAllowed($actionName);
	}
	
	protected function globalPartnerAllowed($actionName)
	{
		if ($this->actionName == 'execute')
			return true;
	}
	
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'executeFromContent') {
			return false;
		}
		if ($actionName === 'executeFromFilters') {
			return false;
		}
		if ($actionName === 'execute') {
			return false;
		}
		return parent::partnerRequired($actionName);
	}
	
	/**
	 * Add new playlist
	 * Note that all entries used in a playlist will become public and may appear in KalturaNetwork
	 *
	 * @action add
	 * @param KalturaPlaylist $playlist
	 * @param bool $updateStats indicates that the playlist statistics attributes should be updated synchronously now
	 * @return KalturaPlaylist
	 */
	function addAction( KalturaPlaylist $playlist , $updateStats = false)
	{
		$dbPlaylist = $playlist->toInsertableObject();
		
		$this->checkAndSetValidUserInsert($playlist, $dbPlaylist);
		$this->checkAdminOnlyInsertProperties($playlist);
		$this->validateAccessControlId($playlist);
		$this->validateEntryScheduleDates($playlist, $dbPlaylist);
		
		$dbPlaylist->setPartnerId ( $this->getPartnerId() );
		$dbPlaylist->setStatus ( entryStatus::READY );
		$dbPlaylist->setKshowId ( null ); // this is brave !!
		$dbPlaylist->setType ( entryType::PLAYLIST );
		
		myPlaylistUtils::validatePlaylist( $dbPlaylist );
		
		$dbPlaylist->save();
		
		if ( $updateStats )
			myPlaylistUtils::updatePlaylistStatistics( $dbPlaylist->getPartnerId() , $dbPlaylist );
		
		$trackEntry = new TrackEntry();
		$trackEntry->setEntryId($dbPlaylist->getId());
		$trackEntry->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY);
		$trackEntry->setDescription(__METHOD__ . ":" . __LINE__ . "::ENTRY_PLAYLIST");
		TrackEntry::addTrackEntry($trackEntry);
		
		$playlist = new KalturaPlaylist(); // start from blank
		$playlist->fromObject( $dbPlaylist );
		
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
		if ( $dbPlaylist->getType() != entryType::PLAYLIST )
			throw new KalturaAPIException ( APIErrors::INVALID_PLAYLIST_TYPE );
			
		if ($version !== -1)
			$dbPlaylist->setDesiredVersion($version);
			
		$playlist = new KalturaPlaylist(); // start from blank
		$playlist->fromObject( $dbPlaylist );
		
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
	 * @validateUser entry id edit
	 */
	function updateAction( $id , KalturaPlaylist $playlist , $updateStats = false )
	{
		$dbPlaylist = entryPeer::retrieveByPK( $id );
		
		if ( ! $dbPlaylist )
			throw new KalturaAPIException ( APIErrors::INVALID_ENTRY_ID , "Playlist" , $id  );
		if ( $dbPlaylist->getType() != entryType::PLAYLIST )
			throw new KalturaAPIException ( APIErrors::INVALID_PLAYLIST_TYPE );
		
		$playlist->playlistType = $dbPlaylist->getMediaType();
		
		// Added the following 2 lines in order to make the permission verifications in toUpdatableObject work on the actual db object
		// TODO: the following use of autoFillObjectFromObject should be replaced by a normal toUpdatableObject
		$tmpDbPlaylist = clone $dbPlaylist;
		$tmpDbPlaylist = $playlist->toUpdatableObject($tmpDbPlaylist);
		
		$playlistUpdate = null;
		$playlistUpdate = $playlist->toUpdatableObject($playlistUpdate);
		
		$this->checkAndSetValidUserUpdate($playlist, $playlistUpdate);
		$this->checkAdminOnlyUpdateProperties($playlist);
		$this->validateAccessControlId($playlist);
		$this->validateEntryScheduleDates($playlist, $dbPlaylist);

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
		if(!is_null($playlistUpdate->getDataContent(true)) && $playlistUpdate->getDataContent(true) != $dbPlaylist->getDataContent())
		{
			$dbPlaylist->setDataContent ( $playlistUpdate->getDataContent(true)  );
			myPlaylistUtils::validatePlaylist( $dbPlaylist );
		}
		
		if ( $updateStats )
			myPlaylistUtils::updatePlaylistStatistics ( $this->getPartnerId() , $dbPlaylist );//, $extra_filters , $detailed );
		
		$dbPlaylist->save();
		$playlist->fromObject( $dbPlaylist );
		
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
	 * @validateUser entry id edit
	 */
	function deleteAction(  $id )
	{
		$this->deleteEntry($id, KalturaEntryType::PLAYLIST);
	}
	
	
	/**
	 * Clone an existing playlist
	 *
	 * @action clone
	 * @param string $id  Id of the playlist to clone
	 * @param KalturaPlaylist $newPlaylist Parameters defined here will override the ones in the cloned playlist
	 * @return KalturaPlaylist
	 *
	 * @throws APIErrors::INVALID_ENTRY_ID
	 * @throws APIErrors::INVALID_PLAYLIST_TYPE
	 */
	function cloneAction( $id, KalturaPlaylist $newPlaylist = null)
	{
		$dbPlaylist = entryPeer::retrieveByPK( $id );
		
		if ( !$dbPlaylist )
			throw new KalturaAPIException ( APIErrors::INVALID_ENTRY_ID , "Playlist" , $id  );
			
		if ( $dbPlaylist->getType() != entryType::PLAYLIST )
			throw new KalturaAPIException ( APIErrors::INVALID_PLAYLIST_TYPE );
			
		if ($newPlaylist->playlistType && ($newPlaylist->playlistType != $dbPlaylist->getMediaType()))
			throw new KalturaAPIException ( APIErrors::CANT_UPDATE_PARAMETER, 'playlistType' );
		
		$oldPlaylist = new KalturaPlaylist();
		$oldPlaylist->fromObject($dbPlaylist);
			
		if (!$newPlaylist) {
			$newPlaylist = new KalturaPlaylist();
		}
		
		$reflect = new ReflectionClass($newPlaylist);
		$props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
		foreach ($props as $prop) {
			$propName = $prop->getName();
			// do not override new parameters
			if ($newPlaylist->$propName) {
				continue;
			}
			// do not copy read only parameters
			if (stristr($prop->getDocComment(), '@readonly')) {
				continue;
			}
			// copy from old to new
			$newPlaylist->$propName = $oldPlaylist->$propName;
		}

		// add the new entry
		return $this->addAction($newPlaylist, true);
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
	    list($list, $totalCount) = parent::listEntriesByFilter($filter, $pager);
	    
	    $newList = KalturaPlaylistArray::fromPlaylistArray($list);
		$response = new KalturaPlaylistListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * Retrieve playlist for playing purpose
	 * @disableTags TAG_WIDGET_SESSION
	 *
	 * @action execute
	 * @param string $id
	 * @param string $detailed
	 * @param KalturaContext $playlistContext
	 * @param KalturaMediaEntryFilterForPlaylist $filter
	 * @return KalturaBaseEntryArray
	 */
	function executeAction( $id , $detailed = false, KalturaContext $playlistContext = null, $filter = null )
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		
		$extraFilters = array();
		if ($filter)
		{
			$coreFilter = new entryFilter();
			$filter->toObject($coreFilter);
			$extraFilters[1] = $coreFilter;
		}
			
		if ($this->getKs() && is_object($this->getKs()) && $this->getKs()->isAdmin())
			myPlaylistUtils::setIsAdminKs(true);

	    $corePlaylistContext = null;
	    if ($playlistContext)
	    {
	        $corePlaylistContext = $playlistContext->toObject();
	        myPlaylistUtils::setPlaylistContext($corePlaylistContext);
	    }
		try
		{
			$entryList= myPlaylistUtils::executePlaylistById( $this->getPartnerId() , $id , $extraFilters , $detailed);
		}
		catch (kCoreException $ex)
		{
			if ($ex->getCode() ==  APIErrors::INVALID_ENTRY_ID)
	    		throw new KalturaAPIException ( APIErrors::INVALID_ENTRY_ID , "Playlist" , $id  );
			else if ($ex->getCode() == APIErrors::INVALID_ENTRY_TYPE)
				throw new KalturaAPIException ( APIErrors::INVALID_PLAYLIST_TYPE );
					    		
    		throw $ex;
		}

		myEntryUtils::updatePuserIdsForEntries ( $entryList );
			
		return KalturaBaseEntryArray::fromEntryArray( $entryList );
	}
	

	/**
	 * Retrieve playlist for playing purpose, based on content
	 * @disableTags TAG_WIDGET_SESSION
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
	 * @disableTags TAG_WIDGET_SESSION
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
		$dbPlaylist->setType ( entryType::PLAYLIST ); // prepare the playlist type before filling from request
		$dbPlaylist->setMediaType ( $playlistType );
		$dbPlaylist->setDataContent( $playlistContent );
				
		myPlaylistUtils::updatePlaylistStatistics ( $this->getPartnerId() , $dbPlaylist );//, $extra_filters , $detailed );
		
		$playlist = new KalturaPlaylist(); // start from blank
		$playlist->fromObject( $dbPlaylist );
		
		return $playlist;
	}
	

}
