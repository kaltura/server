<?php
class mySearchUtils
{
	const MODE_KUSER = "KUSER";
	const MODE_ENTRY = "ENTRY";
	const MODE_KSHOW = "KSHOW";
	const MODE_ALL = "ALL";

	const DISPLAY_IN_SEARCH_SYSTEM = -1;
	const DISPLAY_IN_SEARCH_NONE = 0;
	const DISPLAY_IN_SEARCH_PARTNER_ONLY = 1;
	const DISPLAY_IN_SEARCH_KALTURA_NETWORK = 2;
	
	const KALTURA_NETWORK = "kn";
	
	const SEARCH_ENTRY_TYPE_RC= "_RC_";
	
	const ENTRY_CATEGORY_ID_PREFIX = "_CAT_";
	const ENTRY_DURATION_TYPE_PREFIX = "_DURATION_";
	const ENTRY_FLAVOR_PARAMS_PREFIX = "_FLAVOR_";
	
	public static function search ( $mode , $keywords , $page_index , $page_size = 20 , $base_criteria = null )
	{
		// this is a silly hack until we change the interface to work with an input container rather than the request
		// TODO - replace !
		$_REQUEST["keywords"] = $keywords;
		
		if ( $mode == self::MODE_ENTRY )
		{
			$filter = new entryFilter ();
			$act = new AJAX_getEntriesAction();
			$pager_name = "entry";
		}	
		elseif ( $mode == self::MODE_KSHOW )
		{
			$filter = new kshowFilter ();
			$act = new AJAX_getKshowsAction();
			$pager_name = "kshow";
		}	
		else
		{
			throw new Exception ( "Cannot search in mode [$mode]");	
		}
		
		$map = array ( "page" => $page_index , "keywords" => $keywords );
		// this container will 
		$generic_container = new myGenericContainer( $map );
		
		$pager = new mySmartPager ( $generic_container , $pager_name , $page_size );

/*		// this bellow will bypass the partner filter - at the end of the code the filter will return to be as was before
		$criteria = entryPeer::getCriteriaFilter()->getFilter();		
		$original_partner_to_filter = $criteria->get( entryPeer::PARTNER_ID );
		$criteria->remove (entryPeer::PARTNER_ID  );
	*/	
		
		$act->setIdList( NULL );
		$act->setSortAlias( "ids" );
		
//		$kaltura_media_type = self::getKalturaMediaType ( $media_type );

//		$act->setMediaType ( $media_type );
//		$act->setOnlyForKuser ( $kuser_id );
//		$act->setPublicOnly( true );
		$results = $act->fetchPage( $generic_container , $filter , $pager , $base_criteria );
		
		$number_of_results = $pager->getNumberOfResults();
		$number_of_pages = $pager->getNumberOfPages();	
		
		return array ( $results , $number_of_results , $number_of_pages ); 
	}
	
 // split the phrase by any number of commas or space characters,
 // which include " ", \r, \t, \n and \f
	static public function getKeywordsFromStr ( $str )
	{
		$str = trim ( $str );
		if  ( $str == NULL || $str == "" )
		{
			return NULL;
		}
		//return explode ( " " , $str );
		return preg_split('/[\s,]+/', $str );
	}
  
	
	/**
	 * log the requested keywords with as much date as possible
	 * time,
	 * user_id
	 * str
	 */
	static public function logKeywords ( $action , $keywords )
	{
		// log user id

	}
  
	/**
	 * this function can be used to create a single string that is secure and be passed to the client with no fear it's going to be tamperred.
	 * from the output string
	 */
	public static function encodeListToString ( array $id_list , $key = null )
	{
		$str = serialize( $id_list );
		// encrypt $str
		$enc_str = $str ;
		return $enc_str ;
	}

	// TODO - for now allow id,id,id - must remove !!
	public static function decodeStringToList( $str , $key = null )
	{
		if ( $str == NULL || $str == "" ) return NULL;
		if ( strpos($str , "," ) >= 0 )
		{
			return explode ( "," , $str );
		}

		// decrypt the string
		$dec_str = $str ;

		$id_list = unserialize( $dec_str ) ;
		return $id_list;
	}

	// will return the id list and modify the mode according to the request
	public static function getIdList ( &$mode , &$featured )
	{
		$keywords = @$_REQUEST[ "keywords" ];
		if ( empty ( $keywords ) )
		{
			$featured = @$_REQUEST[ "featured" ];
		}
		else
		{
			$featured = "";
		}
		

		if ( $featured == "kalturas" )
		{
			// in this case it's only about kshows
			$mode = mySearchUtils::MODE_KSHOW;
			$id_list = myFeatureUtils::getFeaturedShowsIdList();
		}
		elseif( $featured == "teams" )
		{
			// in this case it's only about kshows
			$mode = mySearchUtils::MODE_KSHOW;
			$id_list = myFeatureUtils::getFeaturedTeamsIdList();
		}
		else
		{
			$featured = NULL;
			$id_list = NULL ;// mySearchUtils::decodeStringToList( $ids );
		}
		
		return $id_list;
	}
	
	/**
		Will set the 'display_in_search' field according to business-logic per object type
		// kuser | kshow | entry
		// for objects that are search worthy - search_text will hold text from relevant columns depending on the object type 
	*/
	public  static function setDisplayInSearch ( BaseObject $obj , $parent_obj = null )
	{
		if ( $obj == null ) return;
		
		// update the displayInSearch with the logic above only when the object is new or null
		if ( $obj->isNew() || $obj->getDisplayInSearch() === null )
		{
			$res = myPartnerUtils::shouldDisplayInSearch( $obj->getPartnerId() );
			$obj_id =  $obj->getId();
			if ( $obj_id && is_numeric( $obj_id ) )
			{
				self::setRes ( $res , ( $obj_id > entry::MINIMUM_ID_TO_DISPLAY ) );
			}
	
			if ( $res )
			{
				if ( $obj instanceof kuser )
				{
					// if the status is not
					self::setRes ( $res , $obj->getStatus() == KuserStatus::ACTIVE );
				}
				elseif ( $obj instanceof kshow )
				{
					self::setRes ( $res ,  $obj->getViewPermissions() == kshow::KSHOW_PERMISSION_EVERYONE || $obj->getViewPermissions() == null  );
					// if the viewPermission changed from kshow::KSHOW_PERMISSION_EVERYONE to something else 
					// update all entries
					if ( $res && $obj->isColumnModified( kshowPeer::VIEW_PERMISSIONS  ) )
					{
						$entries = $obj->getentrys( ) ;
						foreach ( $entries as $entry )
						{
							// run this code for each entry
							self::setDisplayInSearch( $entry , $obj );
						}
					} 
				} 
				elseif ( $obj instanceof entry )
				{
					// status=READY , type=MEDIACLIP, view permissions of kshow 
					if($obj->getParentEntryId())
						$res = mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM;
					else
						self::setRes ( $res , true );
				}
				else
				{
					throw new Exception ( "mySearchUtils::setDisplayInSearch - cannot handle objects of type " . get_class( $obj) );
				}
			}
			
			$obj->setDisplayInSearch ( $res );
		}
	}
	
	private static function setRes ( &$res , $new_value , $boolean_value= true )
	{
		if ( $boolean_value ) 
		{
			if ( ! $new_value ) $res=0;
			// else - leave untouched
		}
		else
		{
			// this will make things more strict every time according to the new_value
			if ( $res > $new_value) $res = $new_value;
			if ( $res < 0 ) $res = 0;
		}
	}
		
	// don't insert doubles, or small words
	// TODO !!
	public static function prepareSearchText ( $words )
	{ 
		// a  single quote should be removed with no space replacer
		$words = preg_replace ( "/[\r\n'\"]/" , "", $words );
		// all other starnge characters will be ragrded as spaces
		$words = preg_replace ( '/[ \r\t]{2,}/s' , " " , $words ) ; // get rid of multiple spaces
		return $words;
	}
	
	// add to the kaltura network or only to the partner's search text
	public static function addPartner ( $partner_id , $text , $res , $extra_invisible_data = null )
	{
		switch ( $res )
		{
			case 2:
				$prefix = "_KAL_NET_";
				break;
			case 1: 
				$prefix = "_PAR_ONLY_";
				break;
			default:
				$prefix = "_NONE_";
				break;
		}
		
		return "$prefix _{$partner_id}_" . " {$extra_invisible_data}| " . $text; 
	}
	
	public static function removePartner ( $text )
	{
		$index = strpos( $text , "|" );
		if ( $index === FALSE ) return $text;
		return substr ( $text , $index +1 );
	}
	
	public static function getPartnerKeyword ( $partner_id )
	{
		return "_{$partner_id}_";
	}

	public static function getKalturaNetworkKeyword ( )
	{
		return "_KAL_NET_";
	}

	public static function getPartnerOnlyKeyword ( )
	{
		return "_PAR_ONLY_";
	}

	public static function getPartnerNoneKeyword ( )
	{
		return "_NONE_";
	}

	public static function getMd5EncodedString ( $str )
	{
		return md5(strtolower(trim($str)));
	}
	
}
?>