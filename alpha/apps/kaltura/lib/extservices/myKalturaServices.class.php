<?php
//define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);
//require_once(MODULES.'search/actions/entryFilter.class.php');
//require_once(MODULES.'search/actions/AJAX_getEntriesAction.class.php');


class myKalturaServices extends myBaseMediaSource implements IMediaSource
{
	const AUTH_SALT = "myKalturaServices:gogog123";
	const AUTH_INTERVAL = 3600;
	
	const MAX_PAGE_SIZE = 30;
	
	protected $supported_media_types = 7; // support all media//self::SUPPORT_MEDIA_TYPE_VIDEO + (int)self::SUPPORT_MEDIA_TYPE_IMAGE;  
	protected $source_name = "Kaltura";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC );//, self::AUTH_METHOD_USER_PASS);
	protected $search_in_user = true; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_kaltura.gif";
	protected $id = entry::ENTRY_MEDIA_SOURCE_KALTURA;
	
	private static $NEED_MEDIA_INFO = "0";
	
	// TODO - REMOVE !!! this is a sill;y hack because all the functions are static and are VERY hard to inherit.
	// onve the interface's functions are member functions -  we willl not need this (or the silly __construct function of the inheriting class)
	//  
	protected static $s_clazz;
	public function __construct()
	{
		 self::$s_clazz = get_class();
	}
	
	protected function getEntryFilter ( $extraData )
	{
		return new entryFilter ();	
	}

	/**
	 * 
		return array('status' => $status, 'message' => $message, 'objectInfo' => $objectInfo);
	*/
	public function getMediaInfo( $media_type ,$objectId)
	{
		return "";		
	}
	
	
	/**
		return array('status' => $status, 'message' => $message, 'objects' => $objects);
			objects - array of
					'thumb' 
					'title'  
					'description' 
					'id' - unique id to be passed to getMediaInfo 
	*/
	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null , $extraData = null)
	{
		$page_size = $pageSize > self::MAX_PAGE_SIZE ? self::MAX_PAGE_SIZE : $pageSize ;

		$status = "ok";
		$message = '';
		
		
		// this is a silly hack until we change the interface to work with an input container rather than the request
		// TODO - replace !
		$_REQUEST["keywords"] = $searchText;
 
		// TODO  - remove  -see the comment above the __construct() funciton
		$clzz = self::$s_clazz;//get_class ();
		$service = new $clzz();
		$entry_filter = $service->getEntryFilter( $extraData );
		
		$map = array ( "page" => $page , "keywords" => $searchText );
		// this container will 
		$generic_container = new myGenericContainer( $map );
		
		$entry_pager = new mySmartPager ( $generic_container , "entry" , $page_size );

		// this bellow will bypass the partner filter - at the end of the code the filter will return to be as was before
		// don't filter by partner  
		$criteria = entryPeer::getCriteriaFilter()->getFilter();		
		$original_partner_to_filter = $criteria->get( entryPeer::PARTNER_ID );
		$criteria->remove (entryPeer::PARTNER_ID  );
		
		// filter: allow only entries of status READY !
		$criteria->addAnd ( entryPeer::STATUS , entry::ENTRY_STATUS_READY );
		
		
		$act = new AJAX_getEntriesAction();
		$act->setIdList( NULL );
		$act->setSortAlias( "ids" );
		$act->skip_count = true;
		
//		$kaltura_media_type = self::getKalturaMediaType ( $media_type );

		$act->setMediaType ( $media_type );
		
		$fetch = true;
		if ( $authData != null )
		{
			list ( $kuser_id , $hash ) = explode ( "I" , $authData );
			$fetch = false;
			$hash_res  = kString::verifyExpiryHash( $kuser_id , self::AUTH_SALT  , $hash , self::AUTH_INTERVAL );
			
			if ( 0 < $hash_res )
			{
				$fetch = true;
				$act->setOnlyForKuser ( $kuser_id );
			}

			if ( ! $fetch )
			{
				$status = "error";
				$message = "invalid authentication data";
			}
		}
		else
		{
			$act->setPublicOnly( true );
		}
		
		
		if ( $fetch )
		{
			$entry_results = $act->fetchPage( $generic_container , $entry_filter , $entry_pager );
		}
		else
		{
			$entry_results = array ();
			
		}

		// after the query - return the filter to what it was before
		$criteria->addAnd ( entryPeer::PARTNER_ID , $original_partner_to_filter );
		
		
		$number_of_results = $entry_pager->getNumberOfResults();
		$number_of_pages = $entry_pager->getNumberOfPages();	
		
		$objects = array();
		
		// add thumbs when not image or video
		$should_add_thumbs = $media_type != entry::ENTRY_MEDIA_TYPE_AUDIO;
		foreach ( $entry_results as $entry )
		{
			// use the id as the url - it will help using this entry id in addentry
			$object = array ( "id" => $entry->getId() ,
				"url" => $entry->getDataUrl() , 
				"tags" => $entry->getTags() ,
				"title" => $entry->getName() , 
				"description" => $entry->getDescription() ,
				"flash_playback_type" => $entry->getMediaTypeName() ,
//				"partnerId" => $entry->getPartnerId() 
			);
				
			if ( $should_add_thumbs )
			{
				$object["thumb"] = $entry->getThumbnailUrl() ;				
			}
			
			$objects[] = $object;
		}
		
		return array('status' => $status, 'message' => $message, 'objects' => $objects , "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}
	
	
	/**
	*/
	public function getAuthData( $kuserId, $userName, $password, $token)
	{
		$status = 'error';
		$message = '';
		$authData = null;
		
		$kuser = kuserPeer::getKuserByScreenName( $userName );
		if ( $kuser )
		{
			if ( $kuser->isPasswordValid ( $password ) )
			{
				$authData= self::createHashString ( $kuser->getId() );
				
				$status = "ok";
			}
		}
		
		return array('status' => $status, 'message' => $message, 'authData' => $authData );
	}
	
	

	private static function createHashString ( $kuser_id )	
	{
		$hash = kString::expiryHash($kuser_id , self::AUTH_SALT  , self::AUTH_INTERVAL  ) ;
		$authData= $kuser_id . "I" . $hash;
		return $authData;
	}
	
	
}
?>