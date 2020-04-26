<?php
/**
 * Playlist service lets you create,manage and play your playlists
 * Playlists could be static (containing a fixed list of entries) or dynamic (based on a filter)
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
	 *
	 * @disableRelativeTime $playlist
	 * @throws KalturaAPIException
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

		myPlaylistUtils::validatePlaylist($dbPlaylist);

		$dbPlaylist->save();
		
		if ( $updateStats )
			myPlaylistUtils::updatePlaylistStatistics( $dbPlaylist->getPartnerId() , $dbPlaylist );
		
		$trackEntry = new TrackEntry();
		$trackEntry->setEntryId($dbPlaylist->getId());
		$trackEntry->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY);
		$trackEntry->setDescription(__METHOD__ . ":" . __LINE__ . "::ENTRY_PLAYLIST");
		TrackEntry::addTrackEntry($trackEntry);
		
		$playlist = new KalturaPlaylist(); // start from blank
		$playlist->fromObject($dbPlaylist, $this->getResponseProfile());
		
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
	 * @throws KalturaAPIException
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
		$playlist->fromObject($dbPlaylist, $this->getResponseProfile());
		
		return $playlist;
	}

	/**
	 * Update existing playlist
	 * Note - you cannot change playlist type. Updated playlist must be of the same type.
	 *
	 * @action update
	 * @param string $id
	 * @param KalturaPlaylist $playlist
	 * @param bool $updateStats
	 * @return KalturaPlaylist
	 * @throws KalturaAPIException
	 * @validateUser entry id edit
	 *
	 * @disableRelativeTime $playlist
	 */
	function updateAction($id , KalturaPlaylist $playlist , $updateStats = false )
	{
		$dbPlaylist = entryPeer::retrieveByPK($id);

		if(!$dbPlaylist)
		{
			throw new KalturaAPIException (APIErrors::INVALID_ENTRY_ID, "Playlist", $id);
		}

		if ($dbPlaylist->getType() != entryType::PLAYLIST )
		{
			throw new KalturaAPIException (APIErrors::INVALID_PLAYLIST_TYPE);
		}

		$dbPlaylist = $playlist->toUpdatableObject($dbPlaylist);
		$this->checkAndSetValidUserUpdate($playlist, $dbPlaylist);
		$this->checkAdminOnlyUpdateProperties($playlist);
		$this->validateAccessControlId($playlist);
		$this->validateEntryScheduleDates($playlist, $dbPlaylist);

		if(!is_null($dbPlaylist->getDataContent(true)))
		{
			myPlaylistUtils::validatePlaylist($dbPlaylist);
		}

		if ( $updateStats )
		{
			myPlaylistUtils::updatePlaylistStatistics($this->getPartnerId(), $dbPlaylist);//, $extra_filters , $detailed );
		}

		$dbPlaylist->save();
		$playlist->fromObject($dbPlaylist, $this->getResponseProfile());
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
	function deleteAction($id)
	{
		if (!kPermissionManager::isPermitted(PermissionName::PLAYLIST_DELETE))
		{
			$entry = entryPeer::retrieveByPK($id);
			if(!$entry || $entry->getMediaType() != KalturaPlaylistType::STATIC_LIST)
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_MEDIA_TYPE, $id, $entry->getMediaType(), KalturaPlaylistType::STATIC_LIST);
			}
		}

		$this->deleteEntry($id, KalturaEntryType::PLAYLIST);
	}


	/**
	 * Clone an existing playlist
	 *
	 * @action clone
	 * @param string $id Id of the playlist to clone
	 * @param KalturaPlaylist $newPlaylist Parameters defined here will override the ones in the cloned playlist
	 * @return KalturaPlaylist
	 *
	 * @throws APIErrors::INVALID_ENTRY_ID
	 * @throws APIErrors::INVALID_PLAYLIST_TYPE
	 * @throws KalturaAPIException
	 */
	function cloneAction( $id, KalturaPlaylist $newPlaylist = null)
	{
		$dbPlaylist = entryPeer::retrieveByPK( $id );
		
		if ( !$dbPlaylist )
		{
			throw new KalturaAPIException (APIErrors::INVALID_ENTRY_ID, "Playlist", $id);
		}
			
		if ( $dbPlaylist->getType() != entryType::PLAYLIST )
		{
			throw new KalturaAPIException (APIErrors::INVALID_PLAYLIST_TYPE);
		}
			
		if ($newPlaylist->playlistType && ($newPlaylist->playlistType != $dbPlaylist->getMediaType()))
		{
			throw new KalturaAPIException (APIErrors::CANT_UPDATE_PARAMETER, 'playlistType');
		}

		if($dbPlaylist->getMediaType() == PlaylistType::PATH)
		{
			throw new KalturaAPIException(APIErrors::CLONING_PATH_PLAYLIST_NOT_SUPPORTED);
		}

		$oldPlaylist = new KalturaPlaylist();
		$oldPlaylist->fromObject($dbPlaylist, $this->getResponseProfile());
			
		if (!$newPlaylist)
		{
			$newPlaylist = new KalturaPlaylist();
		}
		
		$reflect = new ReflectionClass($newPlaylist);
		$props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
		foreach ($props as $prop)
		{
			$propName = $prop->getName();
			// do not override new parameters
			if ($newPlaylist->$propName)
			{
				continue;
			}
			// do not copy read only parameters
			if (stristr($prop->getDocComment(), '@readonly'))
			{
				continue;
			}
			// copy from old to new
			$newPlaylist->$propName = $oldPlaylist->$propName;
		}

		// add the new entry
		$clonedPlaylist =  $this->addAction($newPlaylist, true);
		self::cloneRelatedObjects($clonedPlaylist->id, $id);
		return $clonedPlaylist;
	}

	protected static function cloneRelatedObjects($clonedPlaylistId, $originalPlaylistId)
	{
		$fileAssetList = FileAssetPeer::retrieveByObject(FileAssetObjectType::ENTRY, $originalPlaylistId);
		foreach ($fileAssetList as $fileAsset)
		{
			$fileAsset->copyToEntry($clonedPlaylistId);
		}
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
	    
	    $newList = KalturaPlaylistArray::fromDbArray($list, $this->getResponseProfile());
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
	 * @param KalturaFilterPager $pager
	 * @return KalturaBaseEntryArray
	 * @ksOptional
	 */
	function executeAction( $id , $detailed = false, KalturaContext $playlistContext = null, $filter = null, $pager = null )
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

		if (in_array($id, kConf::get('partner_0_static_playlists', 'local', array())))
				$playlist = entryPeer::retrieveByPKNoFilter($id);
		else
			$playlist = entryPeer::retrieveByPK($id);

		if (!$playlist)
			throw new KalturaAPIException ( APIErrors::INVALID_ENTRY_ID , "Playlist" , $id  );

		if ($playlist->getType() != entryType::PLAYLIST)
			throw new KalturaAPIException ( APIErrors::INVALID_PLAYLIST_TYPE );

		$entryFilter = null;
		if ($filter)
		{
			$coreFilter = new entryFilter();
			$filter->toObject($coreFilter);
			$entryFilter = $coreFilter;
		}
			
		if ($this->getKs() && is_object($this->getKs()) && $this->getKs()->isAdmin())
			myPlaylistUtils::setIsAdminKs(true);

	    $corePlaylistContext = null;
	    if ($playlistContext)
	    {
	        $corePlaylistContext = $playlistContext->toObject();
	        myPlaylistUtils::setPlaylistContext($corePlaylistContext);
	    }
	    
		// the default of detailed should be true - most of the time the kuse is needed
		if (is_null($detailed))
			 $detailed = true ;

		try
		{
			$entryList = myPlaylistUtils::executePlaylist( $this->getPartnerId() , $playlist , $entryFilter , $detailed, $pager);
		}
		catch (kCoreException $ex)
		{   		
			throw $ex;
		}

		myEntryUtils::updatePuserIdsForEntries ( $entryList );
			
		return KalturaBaseEntryArray::fromDbArray($entryList, $this->getResponseProfile());
	}
	

	/**
	 * Retrieve playlist for playing purpose, based on content
	 * @disableTags TAG_WIDGET_SESSION
	 *
	 * @action executeFromContent
	 * @param KalturaPlaylistType $playlistType
	 * @param string $playlistContent
	 * @param string $detailed
	 * @param KalturaFilterPager $pager
	 * @return KalturaBaseEntryArray
	 */
	function executeFromContentAction($playlistType, $playlistContent, $detailed = false, $pager = null)
	{
		$partnerId = $this->getPartnerId() ? $this->getPartnerId() : kCurrentContext::getCurrentPartnerId();
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		if ($this->getKs() && is_object($this->getKs()) && $this->getKs()->isAdmin())
		{
			myPlaylistUtils::setIsAdminKs(true);
		}
		list($entryFiltersViaEsearch,  $entryFiltersViaSphinx, $totalResults) = myPlaylistUtils::splitEntryFilters($playlistContent);
		$pagerSeparateQueries = self::decideWhereHandlingPager($pager,$entryFiltersViaEsearch, $entryFiltersViaSphinx);
		$entryList = self::handlePlaylistByType($playlistType, $entryFiltersViaEsearch, $entryFiltersViaSphinx, $partnerId, $pagerSeparateQueries, $pager, $totalResults, $playlistContent);
		myEntryUtils::updatePuserIdsForEntries($entryList);
		KalturaLog::debug("entry ids count: " . count($entryList));
		return KalturaBaseEntryArray::fromDbArray($entryList, $this->getResponseProfile());
	}

	protected static function handlePlaylistByType($playlistType, $entryFiltersViaEsearch, $entryFiltersViaSphinx, $partnerId, $pagerSeperateQueries, $pager, $totalResults, $playlistContent)
	{
		$entryList = null;
		switch($playlistType)
		{
			case KalturaPlaylistType::DYNAMIC:
				$entryList = self::handlingDynamicPlaylist($entryFiltersViaEsearch,$entryFiltersViaSphinx, $partnerId, $pagerSeperateQueries, $pager, $totalResults);
				break;
			case KalturaPlaylistType::STATIC_LIST:
			case KalturaPlaylistType::PATH:
				$entryList = myPlaylistUtils::executeStaticPlaylistFromEntryIdsString($playlistContent, null, true, $pager);
				break;
		}

		return $entryList;
	}

	protected static function handlingDynamicPlaylist($entryFiltersViaEsearch, $entryFiltersViaSphinx, $partnerId, $pagerSeparateQueries, $pager, $totalResults)
	{
		$entryListEsearch = array();
		$entryListSphinx = array();
		if ($entryFiltersViaEsearch)
		{
			$entryFiltersViaEsearch = myPlaylistUtils::getEntryFiltersFromXml($entryFiltersViaEsearch, $partnerId);
			list($entryListEsearch, $totalResults) = myPlaylistUtils::executeDynamicPlaylistViaEsearch($entryFiltersViaEsearch, $totalResults, $pagerSeparateQueries);
		}
		if ($entryFiltersViaSphinx)
		{
			$entryListSphinx = myPlaylistUtils::executeDynamicPlaylistFromFilters($totalResults, $entryFiltersViaSphinx, $partnerId, null, true, $pagerSeparateQueries);
		}
		$entryListMerged = array_merge($entryListEsearch,$entryListSphinx);
		$entryListUnique = self::getEntryListUnique($entryListMerged);
		$entryList = self::getEntryListByPager($entryListUnique, $pager, $pagerSeparateQueries);
		return $entryList;
	}

	//When queries are going to run both in Esearch and Sphinx, pager handling will be done after merging the results -> ($pagerSeperateQueries = null)
	//In case only one of them will run (for example only in sphinx), pager handling will be done only there (in this example: in sphinx query) -> ($pagerSeperateQueries = $pager)
	protected static function decideWhereHandlingPager($pager, $entryFiltersViaEsearch, $entryFiltersViaSphinx)
	{
		$pagerSeparateQueries = $pager;
		if ($entryFiltersViaEsearch && $entryFiltersViaSphinx)
		{
			$pagerSeparateQueries = null;
		}
		return $pagerSeparateQueries;
	}

	protected static function getEntryListUnique($entryListMerged)
	{
		$entryList  = array();
		foreach ($entryListMerged as $currentEntry)
		{
			$entryList[$currentEntry->getId()] = $currentEntry;
		}
		return $entryList;
	}

	protected static function getEntryListByPager($entryList, $pager, $pagerSeparateQueries)
	{
		if ( $pager && is_null($pagerSeparateQueries))
		{
			$startOffset = $pager->calcOffset();
			$pageSize = $pager->calcPageSize();
			$entryList = array_slice($entryList, $startOffset, $pageSize);
		}
		return $entryList;
	}
	
	/**
	 * Retrieve playlist for playing purpose, based on media entry filters
	 * @disableTags TAG_WIDGET_SESSION
	 * @action executeFromFilters
	 * @param KalturaMediaEntryFilterForPlaylistArray $filters
	 * @param int $totalResults
	 * @param string $detailed
	 * @param KalturaFilterPager $pager
	 * @return KalturaBaseEntryArray
	 */
	function executeFromFiltersAction(KalturaMediaEntryFilterForPlaylistArray $filters, $totalResults, $detailed = true, $pager = null)
	{
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
	    
		$tempPlaylist = new KalturaPlaylist();
		$tempPlaylist->playlistType = KalturaPlaylistType::DYNAMIC;
		$tempPlaylist->filters = $filters;
		$tempPlaylist->totalResults = $totalResults;
		$tempPlaylist->filtersToPlaylistContentXml();
		return $this->executeFromContentAction($tempPlaylist->playlistType, $tempPlaylist->playlistContent, true, $pager);
	}


	/**
	 * Retrieve playlist statistics
	 * @deprecated
	 * @action getStatsFromContent
	 * @param KalturaPlaylistType $playlistType
	 * @param string $playlistContent
	 * @return KalturaPlaylist
	 */
	function getStatsFromContentAction( $playlistType , $playlistContent )
	{
		die;

	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
	    
		$dbPlaylist = new entry();
		$dbPlaylist->setId( -1 ); // set with some dummy number so the getDataContent will later work properly
		$dbPlaylist->setType ( entryType::PLAYLIST ); // prepare the playlist type before filling from request
		$dbPlaylist->setMediaType ( $playlistType );
		$dbPlaylist->setDataContent( $playlistContent );
				
		myPlaylistUtils::updatePlaylistStatistics ( $this->getPartnerId() , $dbPlaylist );//, $extra_filters , $detailed );
		
		$playlist = new KalturaPlaylist(); // start from blank
		$playlist->fromObject($dbPlaylist, $this->getResponseProfile());
		
		return $playlist;
	}
	

}
