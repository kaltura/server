<?php
//define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);
//require_once(MODULES.'search/actions/entryFilter.class.php');
//require_once(MODULES.'search/actions/AJAX_getEntriesAction.class.php');


class myKalturaKshowServices extends myBaseMediaSource implements IMediaSource
{
	const KALTURA_SERVICE_CRITERIA_FROM_KSHOW = 1;
	const KALTURA_SERVICE_CRITERIA_FROM_ROUGHCUT = 2;
	
	static $s_default_count_limit = 100;
	
	const AUTH_SALT = "myKalturaServices:gogog123";
	const AUTH_INTERVAL = 3600;
	
	const MAX_PAGE_SIZE = 30;
	
	protected $supported_media_types = 7; // support all media//self::SUPPORT_MEDIA_TYPE_VIDEO + (int)self::SUPPORT_MEDIA_TYPE_IMAGE;  
	protected $source_name = "Kaltura";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC );//, self::AUTH_METHOD_USER_PASS);
	protected $search_in_user = true; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_kaltura.gif";
	protected $id = entry::ENTRY_MEDIA_SOURCE_KALTURA_KSHOW;
	
	private static $NEED_MEDIA_INFO = "0";
	
	protected function getKshowFilter ( $extraData )
	{
		return new kshowFilter ();	
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

		this service will first return the relevant kshows, then find the relevant roughcuts and finally fetch the entries
	*/
	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null , $extraData = null)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;

		// this bellow will bypass the partner filter - at the end of the code the filter will return to be as was before
		$kshow_criteria = kshowPeer::getCriteriaFilter()->getFilter();		
		$original_kshow_partner_to_filter = $kshow_criteria->get( kshowPeer::PARTNER_ID );
		$kshow_criteria->remove (kshowPeer::PARTNER_ID  );
		
		$entry_criteria = entryPeer::getCriteriaFilter()->getFilter();		
		$original_entry_partner_to_filter = $entry_criteria->get( entryPeer::PARTNER_ID );
		$entry_criteria->remove (entryPeer::PARTNER_ID  );
		
		$page_size = $pageSize > self::MAX_PAGE_SIZE ? self::MAX_PAGE_SIZE : $pageSize ;

		$status = "ok";
		$message = '';

		$kshow_filter = $this->getKshowFilter( $extraData );

		$limit = $pageSize;
		$offset = $pageSize * ($page-1); // $page starts from 1
		
//		$keywords_array = mySearchUtils::getKeywordsFromStr ( $searchText );

		// TODO_ change mechanism !
		//$search_mechanism = self::KALTURA_SERVICE_CRITERIA_FROM_KSHOW;
		$search_mechanism = self::KALTURA_SERVICE_CRITERIA_FROM_ROUGHCUT;
		
		// TODO - optimize the first part of the entry_id search
		// cache once we know the kshow_ids / roughcuts - this will make paginating much faster
		$kshow_crit = new Criteria();
		$kshow_crit->clearSelectColumns()->clearOrderByColumns();
		$kshow_crit->addSelectColumn(kshowPeer::ID);
		$kshow_crit->addSelectColumn(kshowPeer::SHOW_ENTRY_ID);
		$kshow_crit->setLimit( self::$s_default_count_limit );
		$kshow_filter->addSearchMatchToCriteria( $kshow_crit , $searchText , kshow::getSearchableColumnName() );
		
		if( $search_mechanism == self::KALTURA_SERVICE_CRITERIA_FROM_KSHOW )
		{
			$kshow_crit->add ( kshowPeer::ENTRIES , 1 , criteria::GREATER_EQUAL ) ;
		}						
		
		$rs = kshowPeer::doSelectStmt( $kshow_crit );
		
		
		$kshow_arr = array();
		$roughcut_arr = array(); 
	
		$res = $rs->fetchAll();
		foreach($res as $record) 
		{
			$kshow_arr[] = $record[0];
			$roughcut_arr[] = $record[1];
		}
		
//		// old code from doSelectRs
//		while($rs->next())
//		{
//			$kshow_arr[] = $rs->getString(1);
//			$roughcut_arr[] = $rs->getString(2);
//		}
			

		$crit = new Criteria();
		$crit->setOffset( $offset );
		$crit->setLimit( $limit );
		$crit->add ( entryPeer::TYPE ,  entryType::MEDIA_CLIP );
		$crit->add ( entryPeer::MEDIA_TYPE , $media_type );
		if( $search_mechanism == self::KALTURA_SERVICE_CRITERIA_FROM_KSHOW )
		{
			$crit->add ( entryPeer::KSHOW_ID , $kshow_arr , Criteria::IN );
			$entry_results = entryPeer::doSelect ( $crit );
		}
		elseif (  $search_mechanism == self::KALTURA_SERVICE_CRITERIA_FROM_ROUGHCUT )
		{
//			$entry_results  = roughcutEntryPeer::retrievByRoughcutIds ( $crit , $roughcut_arr , true );
			$entry_results  = roughcutEntryPeer::retrievEntriesByRoughcutIds ( $crit , $roughcut_arr  );
		}
		
		
		
		// after the query - return the filter to what it was before
		$entry_criteria->addAnd ( entryPeer::PARTNER_ID , $original_entry_partner_to_filter );
		$kshow_criteria->addAnd ( kshowPeer::PARTNER_ID , $original_kshow_partner_to_filter );
		
		
		$objects = array();
		
		// add thumbs when not image or video
		$should_add_thumbs = $media_type != entry::ENTRY_MEDIA_TYPE_AUDIO;
		foreach ( $entry_results as $obj )
		{
			if ( $search_mechanism == self::KALTURA_SERVICE_CRITERIA_FROM_KSHOW )
			{
				$entry = $obj;
			}
			else
			{
				//$entry = $obj->getEntry();
				$entry = $obj;
			}
			
			// use the id as the url - it will help using this entry id in addentry
			$object = array ( "id" => $entry->getId() ,
				"url" => $entry->getDataUrl() , 
				"tags" => $entry->getTags() ,
				"title" => $entry->getName() , 
				"description" => $entry->getTags() ,
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