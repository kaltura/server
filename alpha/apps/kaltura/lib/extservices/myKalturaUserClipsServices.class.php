<?php
/**
 * Will return all clips for kuser (in specific partner_id - a kuser is always in a partner context)
 */
class myKalturaUserClipsServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types = 7; // support all media//self::SUPPORT_MEDIA_TYPE_VIDEO + (int)self::SUPPORT_MEDIA_TYPE_IMAGE;  
	protected $source_name = "My Clips";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC );
	protected $search_in_user = false; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_kaltura.gif";
	protected $id = entry::ENTRY_MEDIA_SOURCE_KALTURA_USER_CLIPS;
	
	private static $NEED_MEDIA_INFO = "0";
	
	/**
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
	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData = null)
	{
		$page_size = $pageSize > 20 ? 20 : $pageSize ;
		$page--;
		if ( $page < 0 ) $page = 0;
		
		$status = "ok";
		$message = '';
		$objects = array();
		
		$should_serach = true;
		if (defined("KALTURA_API_V3"))
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(self::$partner_id, self::$puser_id);
			$should_serach = true;
			$kuser_id = $kuser->getId();
		}
		else
		{
			$puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid ( self::$partner_id , self::$subp_id, self::$puser_id , true );		
			if ( ! $puser_kuser )
			{
				// very bad - does not exist in system
				$should_serach = false;  
			}
			else
			{
				$kuser = $puser_kuser->getKuser();
				if ( !$kuser )
				{
					$should_serach = false; 
				}
				else
				{
					$kuser_id = $kuser->getId();
				}
			}
		}
		
//		echo "[" . self::$partner_id . "],[".  self::$subp_id . "],[" . self::$puser_id . "],[$kuser_id]";
		
		if ( $should_serach )
		{
			$c = KalturaCriteria::create("entry");
			$c->add ( entryPeer::KUSER_ID , $kuser_id );
			$c->add ( entryPeer::MEDIA_TYPE , $media_type );
			$c->add ( entryPeer::TYPE , entry::ENTRY_TYPE_MEDIACLIP );
	
			$keywords_array = mySearchUtils::getKeywordsFromStr ( $searchText );
			$filter = new entryFilter();
			$filter->setPartnerSearchScope(self::$partner_id);

			$filter->addSearchMatchToCriteria($c, $keywords_array, entry::getSearchableColumnName() );
						
			$c->setLimit( $pageSize );
			$c->setOffset( $page * $pageSize );
			$entry_results = entryPeer::doSelect ( $c );//JoinAll( $c );
	
			$number_of_results = $c->getRecordsCount();
			$number_of_pages = (int)($number_of_results / $pageSize);
			if ( $number_of_results % $pageSize != 0 ) $number_of_pages += 1; // if there are some left-overs - there must be a nother page
			
			// add thumbs when not image or video
			$should_add_thumbs = $media_type != entry::ENTRY_MEDIA_TYPE_AUDIO;
			foreach ( $entry_results as $entry )
			{
				// send the id as the url
				$object = array ( "id" => $entry->getId() ,
					"url" => $entry->getDataUrl() , 
					"tags" => $entry->getTags() ,
					"title" => $entry->getName() , 
					"description" => $entry->getTags() );
					
				if ( $should_add_thumbs )
				{
					$object["thumb"] = $entry->getThumbnailUrl() ;				
				}
				
				$objects[] = $object;
			}
		}
		return array('status' => $status, 'message' => $message, 'objects' => $objects , "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}
	
	
	/**
	*/
	public function getAuthData( $kuserId, $userName, $password, $token)
	{

	}
	
	

}
?>