<?php
/**
 * Search service allows you to search for media in various media providers
 * This service is being used mostly by the CW component
 *
 * @service search
 * @package api
 * @subpackage services
 */
class SearchService extends KalturaBaseService 
{
	/**
	 * Search for media in one of the supported media providers
	 * 
	 * @action search
	 * @param KalturaSearch $search A KalturaSearch object contains the search keywords, media provider and media type
	 * @param KalturaFilterPager $pager
	 * @return KalturaSearchResultResponse
	 *
	 * @throws APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE
	 * @throws APIErrors::SEARCH_UNSUPPORTED_MEDIA_TYPE
	 */
	public function searchAction( KalturaSearch $search , KalturaFilterPager $pager = null )
	{
		$partnerId = $this->getPartnerId();
			
		if (!$search->searchSource)
			throw new KalturaAPIException ( APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE , $search->searchSource );

		$mediaSourceProvider = myMediaSourceFactory::getMediaSource ( $search->searchSource );
		if ($mediaSourceProvider)
		{
			$mediaSourceProvider->setUserDetails ( $partnerId , $partnerId * 100 , $this->getKuser()->getPuserId() );
			
			if ( ! $mediaSourceProvider )
				throw new KalturaAPIException ( APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE , $search->searchSource );
			
			// temp hack to support cw sending the old parameter
			if ($search->extraData === null && isset($_POST["extra_data"]))
				$search->extraData = $_POST["extra_data"];
				
			$extraData = str_replace( '$partner_id' , $partnerId, $search->extraData );
			
			// temp hack to support cw sending the old parameter
			if ($search->authData === null && isset($_POST["auth_data"]))
				$search->authData = $_POST["auth_data"];
			
			if (!$pager)
				$pager = new KalturaFilterPager;
			if ( ! $pager->pageIndex ) $pager->pageIndex = 1;
			if ( ! $pager->pageSize ) $pager->pageSize = 20;
			
			$results = $mediaSourceProvider->searchMedia ( $search->mediaType , $search->keyWords , $pager->pageIndex , $pager->pageSize , $search->authData , $extraData);
			if ( ! $results )
				throw new KalturaAPIException( APIErrors::SEARCH_UNSUPPORTED_MEDIA_TYPE, $search->mediaType );

			$searchResults = KalturaSearchResultArray::fromSearchResultArray( $results['objects'] , $search );
			$searchResultResponse = new KalturaSearchResultResponse();
			$searchResultResponse->objects = $searchResults;
			$searchResultResponse->needMediaInfo = (bool)$results["needMediaInfo"];
			return $searchResultResponse;
		}
	}
	
	/**
	 * Retrieve extra information about media found in search action
	 * Some providers return only part of the fields needed to create entry from, use this action to get the rest of the fields.
	 * 
	 * @action getMediaInfo
	 * @param KalturaSearchResult $searchResult KalturaSearchResult object extends KalturaSearch and has all fields required for media:add
	 * @return KalturaSearchResult
	 *
	 * @throws APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE
	 * @throws APIErrors::SEARCH_UNSUPPORTED_MEDIA_TYPE
	 */
	public function getMediaInfoAction( KalturaSearchResult $searchResult )
	{
		$mediaSourceProvider = myMediaSourceFactory::getMediaSource ( $searchResult->searchSource );
		if ( ! $mediaSourceProvider )
			throw new KalturaAPIException( APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE, $searchResult->searchSource );
		
		$result = $mediaSourceProvider->getMediaInfo ( $searchResult->mediaType , $searchResult->id );
		
		if ( ! $result )
			throw new KalturaAPIException ( APIErrors::SEARCH_UNSUPPORTED_MEDIA_TYPE, $searchResult->mediaType );

		$newSearchResult = new KalturaSearchResult;
		$search = new KalturaSearch;
		$search->keyWords = $searchResult->keyWords;
		$search->mediaType = $searchResult->mediaType;
		$search->searchSource = $searchResult->searchSource;
		
		$newSearchResult->fromSearchResult( $result['objectInfo'] , $search );
		
		return $newSearchResult;
	}
	
	/**
	 * Search for media given a specific URL
	 * Kaltura supports a searchURL action on some of the media providers.
	 * This action will return a KalturaSearchResult object based on a given URL (assuming the media provider is supported)
	 * 
	 * @action searchUrl
	 * @param KalturaMediaType $mediaType
	 * @param string $url
	 * @return KalturaSearchResult
	 *
	 * @throws APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE_FOR_URL
	 */
	public function searchUrlAction ( $mediaType , $url )
	{
		list ( $mediaSourceProvider ,$objId ) = myMediaSourceFactory::getMediaSourceAndObjectDataByUrl( $mediaType , $url );
		if ( ! $mediaSourceProvider )
			throw new KalturaAPIException( APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE_FOR_URL, $url );
		
		$result = $mediaSourceProvider->getMediaInfo ( $mediaType , $objId );
		
		$newSearchResult = new KalturaSearchResult;
		$search = new KalturaSearch;
		$newSearchResult->fromSearchResult( $result['objectInfo'] , $search );
		return $newSearchResult;
	}
	
	/**
	 * 
	 * @action externalLogin
	 * @param KalturaSearchProviderType $searchSource
	 * @param string $userName
	 * @param string $password
	 * @return KalturaSearchAuthData 
	 */
	public function externalLoginAction($searchSource, $userName, $password)
	{
		$mediaSourceProvider = myMediaSourceFactory::getMediaSource($searchSource);
		if (!$mediaSourceProvider)
			throw new KalturaAPIException(KalturaErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE, $searchSource);
			
		$resultArray = $mediaSourceProvider->getAuthData($this->getKuser()->getId(), $userName, $password, "");
		
		$result = new KalturaSearchAuthData();
		$result->fromArray($resultArray);
		return $result;
	}
}