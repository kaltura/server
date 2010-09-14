<?php
//define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);
//require_once(MODULES.'search/actions/entryFilter.class.php');
//require_once(MODULES.'search/actions/AJAX_getEntriesAction.class.php');


class extStroomeServices extends myBaseMediaSource implements IMediaSource
{
	const STROOME_SEARCH_TYPE_PUBLIC = 1;
	const STROOME_SEARCH_TYPE_FRIENDS = 2;
	const STROOME_SEARCH_TYPE_GROUPS = 3;

	const LIKE_PUBLIC = "%public%";

	const AUTH_SALT = "myKalturaServices:gogog123";
	const AUTH_INTERVAL = 3600;

	const MAX_PAGE_SIZE = 30;

	protected $supported_media_types = 7; // support all media//self::SUPPORT_MEDIA_TYPE_VIDEO + (int)self::SUPPORT_MEDIA_TYPE_IMAGE;
	protected $source_name = "Stroome";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC );//, self::AUTH_METHOD_USER_PASS);
	protected $search_in_user = true;
	protected $logo = "http://www.kaltura.com/images/wizard/logo_kaltura.gif";
	protected $id = 100;


	private static $NEED_MEDIA_INFO = "0";

	/*
	 * will return the hard-coded partnerId for stroome
	 */
	private function getPartnerId()
	{
		return 66122;
//		return 0;
	}

	/**
	 * @param unknown_type $extraData
	 * @return entryFilter
	 */
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
		// make sure the context is of the correct partner 
		if ( $this->getPartnerId() != kCurrentContext::$ks_partner_id )
		{
			Throw new APIException( APIErrors::INVALID_ACCESS_TO_PARTNER_SPECIFIC_SEARCH ,  kCurrentContext::$ks_partner_id ); 	
		}
		
		$page_size = $pageSize > self::MAX_PAGE_SIZE ? self::MAX_PAGE_SIZE : $pageSize ;

		// set the user to be the one from the currentContext

		$uid = kCurrentContext::$uid;
		// this bellow will bypass the partner filter - at the end of the code the filter will return to be as was before
		// don't filter by partner
		$criteria = entryPeer::getCriteriaFilter()->getFilter();
		$original_partner_to_filter = $criteria->get( entryPeer::PARTNER_ID );
		$criteria->remove (entryPeer::PARTNER_ID  );


		/*
		 * Will work in one of 3 modes, depending on the "search_type" token passed from the KCW
		 * 1. public - will search all content for partner stroome marked public (admin_tags like "%public%")
		 * 2. friends - will search for all the friends of the user in Stroome usersByUser service amd filter all the entries that belong to the
		 * 		user list that returns
		 * 3. groups - will search for all the groups of the user in Stroome's userByUser service and fillter all the etries that with
		 * 		admin_tag like "%groupX%" or admin_tag like "%groupY%"
		 *
		 * to all criterias the keywords will be added  to NARROW the results - no keywords will return all the results that fit the criteria as
		 * explained above (as opposed to returning NO results)
		 *
		 *
		 */
		$status = "ok";
		$message = '';

		$search_type = @$_REQUEST["search_type"]; // this is an addition sent from the KCW
		$stroome_media_type = isset($_REQUEST["stroome_media_type"]) ? $_REQUEST["stroome_media_type"] : null; // so we can pass 6 to search mixes
		if ( ! $search_type ) // set to the default
		$search_type = self::STROOME_SEARCH_TYPE_PUBLIC ;
			
		// use the slave for all queries
		//		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;

		$c = new EntrySphinxCriteria();

		$c->addAnd ( entryPeer::PARTNER_ID , $this->getPartnerId() );
		// filter: allow only entries of status READY !
		$c->addAnd ( entryPeer::STATUS , entry::ENTRY_STATUS_READY );

		// narrow done to video  | audio | image
		if ($stroome_media_type)
		{
			$c->addAnd ( entryPeer::MEDIA_TYPE , $stroome_media_type );
		}
		elseif ( $media_type )
		{
			$c->addAnd ( entryPeer::MEDIA_TYPE , $media_type );
		}

		$entry_filter = $this->getEntryFilter( null );
		$entry_filter->setPartnerSearchScope( $this->getPartnerId() );
		$entry_filter->addSearchMatchToCriteria( $c , $searchText , entry::getSearchableColumnName() );

		$skip_query = false;
		// cache the queries from the usersByUser service
		if ( $search_type == self::STROOME_SEARCH_TYPE_PUBLIC )
		{
			$c->addAnd ( entryPeer::ADMIN_TAGS ,self::LIKE_PUBLIC , Criteria::LIKE );
		}
		elseif ( $search_type == self::STROOME_SEARCH_TYPE_FRIENDS )
		{
			$puser_id_list = self::getListOfFriendIds ( $uid );
				
			// create a kuser_list array from the puser_id_list string
			$kuser_id_list = self::getKuserIdsFromPuserIds ($puser_id_list );
			if ( count($kuser_id_list) > 0 )
			{
				$c->addAnd ( entryPeer::KUSER_ID , $kuser_id_list , Criteria::IN );
			}
			else
			{
				$skip_query = true;
				$skip_reason = "User has no friends";
				$c->addAnd( entryPeer::KUSER_ID , -1 ); // make sure will find no kusers
			}
		}
		elseif ( $search_type == self::STROOME_SEARCH_TYPE_GROUPS )
		{
			$groups = self::getListOfGroups ( $uid );
				
			if ( $groups )
			{
				$groups_str = implode ( "," , $groups );
				// add the filters ability to handle multi-like-or for the admin tags
				$entry_filter->setByName( "_mlikeor_admin_tags" , $groups_str );
			}
			else
			{
				$skip_query = true;
				$skip_reason = "User belongs to no groups";
				$c->addAnd ( entryPeer::ADMIN_TAGS , -1 );
			}
		}

		if ( ! $skip_query )
		{
			$c->setLimit ( $page_size );
			$c->setOffset ( ($page-1) * $page_size ); // ZERO based paging
				
			$entry_filter->attachToCriteria( $c );
			$entry_results = entryPeer::doSelect( $c );
			$number_of_results = $c->getSphinxRecordsCount();
				
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
					"description" => $entry->getTags() ,
				//				"partnerId" => $entry->getPartnerId()
				);
					
				if ( $should_add_thumbs )
				{
					$object["thumb"] = $entry->getThumbnailUrl() ;
				}

				$objects[] = $object;
			}
		}
		else
		{
			$objects = array();
			KalturaLog::log( "Skipping query [$skip_reason]...");
		}
		return array('status' => $status, 'message' => $message, 'objects' => $objects , "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}


	public function getAuthData( $kuserId, $userName, $password, $token)
	{
		return;
	}



	/**
	 * will return a list of puser_ids from the usersByUser service
	 *
	 * @param string $puser_id
	 * @return array
	 */
	private static function getListOfFriendIds ( $puser_id )
	{
		list ( $json_result , $error , $users , $groups ) = stroomeService::postForUid( $puser_id );
		return $users;
	}

	/**
	 * will return a list of groups the user belongs to 
	 * 
	 * @param unknown_type $puser_id
	 * @return array
	 */
	private static function getListOfGroups ( $puser_id )
	{
		list ( $json_result , $error , $users , $groups ) = stroomeService::postForUid( $puser_id );
		return $groups;
	}


	/**
	 * Will return a list of all the kuser_ids that belong to the puser_ids
	 *
	 * @param array $puser_id_list
	 * @return array
	 */
	private static function getKuserIdsFromPuserIds ( $puser_id_list )
	{
		/*
		$puser_id_arr_raw = explode ("," , $puser_id_list );
		$puser_id_arr = array();
		foreach ( $puser_id_arr_raw as $puser_id)
		{
			if (  trim ( $puser_id ))
			$puser_id_arr[] = trim ( $puser_id );
		}
		*/
		$puser_id_arr = $puser_id_list;
		if ( count($puser_id_arr) ==0)
		return null;
			
		$c = new Criteria();
		$c->add ( PuserKuserPeer::PARTNER_ID , self::$partner_id );
		$c->add ( PuserKuserPeer::PUSER_ID , $puser_id_arr , Criteria::IN );

		$puser_kuser_list = PuserKuserPeer::doSelect( $c );
			
		$kuser_id_arr = array();
		foreach ( $puser_kuser_list as $puser_kuser )
		{
			$kuser_id_arr[] = $puser_kuser->getKuserId();
		}

		return $kuser_id_arr;
	}

}


class stroomeService
{
/*
 * 
Kaltura Server at www.kaltura.com
d224cd98e2578d4528a62968bad150a2

Kaltura Test Server at kaldev.kaltura.com
fc222f71aa79c53014a1747944ba05de


		respons in a JSON format:
	 * { "#error": false, "#data": { "users": [ "user_14", "user_7", "user_6", "user_4" ], "groups": [  ] } }
	 * 
 * 
 */	
	// the code bellow was sent as an example by them 
	public static function postForUid ( $uid )
	{
		$method = 'kce.get';
		$dav = false;
		if ( $dav )
		{
			$api_key = 'fc222f71aa79c53014a1747944ba05de'; // Your API key.
			$domain = 'kaldev.kaltura.com';
		}
		else
		{
			$api_key = 'd224cd98e2578d4528a62968bad150a2'; // Your API key.
			$domain = 'www.kaltura.com';
		}
		
		$sessid = '9999your9999sess9999id'; // Your session ID.		
		$timestamp = (string) time();
		$nonce = self::user_password();
		$hash = hash_hmac('sha256', $timestamp .';'.$domain .';'. $nonce .';'. $method, $api_key);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, 'http://www.stroome.com/services/json');

		//prepare the field values being posted to the JSON service (WITH key authentication)
		$data = array(
		    'method' => '"'. $method .'"',
		    'hash' => '"'. $hash .'"',
		    'domain_name' => '"'. $domain .'"',
		    'domain_time_stamp' => '"'. $timestamp .'"',
		    'nonce' => '"'. $nonce .'"',
				//'sessid' => '"'. $sessid .'"',  If you're using sessid, uncomment this line
		    'uid' => '"' . $uid . '"',
		);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		//make the request
		$raw_result = curl_exec($ch);

		$json_result = json_decode ( $raw_result ); 
		$res_arr = (array)($json_result);
		$error = $res_arr["#error"];
		if ( $error )
		{
			$users = null;
			$groups = null;
			$data_arr = (array)$res_arr["#data"];
			$error = "Error";
		}
		else
		{
			$data_arr = (array)$res_arr["#data"];
			if ( is_array( $data_arr ) )
			{
				$users = @array_values( $data_arr["users"] );
				$groups= @array_values( $data_arr["groups"] );
			}
			else
			{
				$users = null;
				$groups = null;	
			}
		}
		return array ( $json_result , $error , $users , $groups );
	}
	
	/**
	 * Generate a random alphanumeric password.
	 */
	private static function user_password($length = 10) {
	  // This variable contains the list of allowable characters for the
	  // password. Note that the number 0 and the letter 'O' have been
	  // removed to avoid confusion between the two. The same is true
	  // of 'I', 1, and 'l'.
	  $allowable_characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

	  // Zero-based count of characters in the allowable list:
	  $len = strlen($allowable_characters) - 1;

	  // Declare the password as a blank string.
	  $pass = '';

	  // Loop the number of times specified by $length.
	  for ($i = 0; $i < $length; $i++) {

	    // Each iteration, pick a random character from the
	    // allowable string and append it to the password:
	    $pass .= $allowable_characters[mt_rand(0, $len)];
	  }

	  return $pass;
	}	
}
?>