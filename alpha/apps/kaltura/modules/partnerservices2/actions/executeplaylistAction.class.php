<?php
/**
 * @package api
 * @subpackage ps2
 */
class executeplaylistAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "executePlaylist",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"playlist_id" => array ("type" => "integer", "desc" => "")
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
	
	private $playlist;
	
	private $access_control_ids;
	

	protected function getExecutionCacheKey ( $partner_id , $subp_id , $puser_id  )
	{
		return $this->executeImpl( $partner_id , $subp_id , $puser_id , null , null , true );
	}
	
	protected function setExtraFilters ( entryFilter &$fields_set )	{	}
	
	protected function joinOnDetailed () { return true;}

	protected function getObjectPrefix () { return "entries"; } 

	protected function partnerGroup2() {return  kCurrentContext::$ks_partner_id . ',0';}
	
	protected function kalturaNetwork2() {return null;}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser , $create_cachekey=false)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		// TODO -  verify permissions for viewing lists

		$detailed = $this->getP ( "detailed" , false );
		if (!$detailed)
			$detailed = false;

		$playlist_id= $this->getPM ( "playlist_id" );
		
		if ( $create_cachekey ) 
		{
			if ( $this->isAdmin() )
				return null;		

			$ks_partner_id = null; 
			$privileges = null;
				
			$ks = ks::fromSecureString(kCurrentContext::$ks);
			if($ks)
			{
				$ks_partner_id = $ks->getPartnerId();
				$privileges = $ks->getPrivileges();
			}
				
			$cache_key_arr = array ( 
				"playlist_id" => $playlist_id , 
				"partner_id" => $partner_id ,
				"ks_partner_id" => $ks_partner_id ,  
				"detailed" => $detailed,
				"user" => kCurrentContext::$ks_uid,
				"privileges" => $privileges,
				"is_admin" => $this->isAdmin(),
				"protocol" => infraRequestUtils::getProtocol(),
			);
			$cahce_key = new executionCacheKey( );
			$cahce_key->expiry =  600 ;
			$cahce_key->key = md5( print_r($cache_key_arr,true ) );
			return $cahce_key;
		}
		
		// this service is executed twice! (first time for the cache key, second time for the execution)
		if (is_null($this->playlist))
		{
			$playlist = entryPeer::retrieveByPK($playlist_id);
			if (!$playlist)
				throw new APIException(APIErrors::INVALID_ENTRY_ID, "Playlist", $playlist_id) ;
				 
			myPartnerUtils::addPartnerToCriteria('accessControl', $playlist->getPartnerId() , $this->getPrivatePartnerData(), $this->partnerGroup2(), null);
			
			$this->playlist = $playlist;
		}
		
		if ($this->isAdmin())
			myPlaylistUtils::setIsAdminKs(true);

		$entry_list = myPlaylistUtils::executePlaylistById( $partner_id , $playlist_id , null , $detailed );

		myEntryUtils::updatePuserIdsForEntries ( $entry_list );
		
		$level = $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR ;
		$wrapper =  objectWrapperBase::getWrapperClass( $entry_list  , $level );
		$this->addMsg ( "count" , count ( $entry_list )) ;
		$this->addMsg ( $this->getObjectPrefix() , $wrapper ) ;
	}
}
?>