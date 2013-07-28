<?php
class myPartnerUtils
{
	const PARTNER_SET_POLICY_NONE = 1;
	const PARTNER_SET_POLICY_IF_NULL = 2;
	const PARTNER_SET_POLICY_FORCE = 3;

	const PUBLIC_PARTNER_INDEX = 99;

	const PARTNER_GROUP = "__GROUP_PARTNER__";
	
	const ALL_PARTNERS_WILD_CHAR = "*";
	
	
	private static $s_current_partner_id = null;
	private static $s_set_partner_id_policy  = self::PARTNER_SET_POLICY_NONE;

	private static $s_filterred_peer_list = array();
	private static $partnerCriteriaParams = array();
	 
	public static function getUrlForPartner ( $partner_id , $subp_id  )
	{
		return "/p/$partner_id/sp/$subp_id";	
	}
	
	public static function shouldDisplayInSearch ( $partner_id )
	{
		// if the partner_id is null - for now - assume should be displayed
		if ( $partner_id == null ) return true;

		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( ! $partner )
			return false;
		return ( $partner->getAppearInSearch() );
	}

	public static function getPrefix ( $partner_id , $padding = true)
	{
		if ( empty ( $partner_id ) ) return "";

		$prefix = null; 
		if ( ! $prefix )
		{
			$partner = PartnerPeer::retrieveByPK( $partner_id );
			if ( ! $partner )
				return null;
			$prefix = $partner->getPrefix();
		}

		if ( $prefix && $padding )
		{
			$prefix = "_" . $prefix . "_";
		}

		return $prefix;
	}

	/**
	 * checks if the secret matchs the partner_id -
	 * if not - increment the invlid_login_count and make sure does not exceed the limit
	 *
	 * will use cache to reduce the times the partner table is hit (hardly changes over time)
	 */
	public static function isValidSecret ( $partner_id , $partner_secret , $partner_key , &$ks_max_expiry_in_seconds , $admin = SessionType::USER  )
	{
		// TODO - handle errors
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner ) {
			return Partner::VALIDATE_WRONG_LOGIN;
		}
		if ($partner->getStatus() != Partner::PARTNER_STATUS_ACTIVE) {
			return Partner::VALIDATE_PARTNER_BLOCKED;
		}
		
		return $partner->validateSecret( $partner_secret , $partner_key , $ks_max_expiry_in_seconds , $admin);
	}

	/**
	 * a lks  is a "lite" kaltura session. It is created by the partner and can be be translated into a simplified ks:
	 * 	1. only regular - not admin
	 * 	2. view & edit privileges (nt for a specific ks)
	 * 	3. does not expire  
	 * 
	 * 	lks = md5 ( secret , puser_id , version )
	 */
	public static function isValidLks ( $partner_id , $lks , $puser_id , $version , &$ks_max_expiry_in_seconds   )
	{
		// TODO - handle errors
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner ) return Partner::VALIDATE_WRONG_LOGIN;
		if ( !$partner->getAllowLks() )	 return Partner::VALIDATE_LKS_DISABLED;
			
		$our_hash = md5 ( $partner->getSecret() . $puser_id . $version );
		$ks_max_expiry_in_seconds = $partner->getKsMaxExpiryInSeconds();
		if ( $lks != $our_hash ) return ks::INVALID_LKS;
		return ( ks::OK );
	}
	
	
	public static function getExpiry ( $partner_id )
	{
		// TODO - handle errors
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner ) return Partner::VALIDATE_WRONG_LOGIN;

		return $partner->getKsMaxExpiryInSeconds( );
	}

	public static function setCurrentPartner ( $partner_id )
	{
		self::$s_current_partner_id = $partner_id;
	}

	public static function resetPartnerFilter($objectName)
	{
		$peerName = $objectName . 'Peer';
		
		$objectName = strtolower($objectName);
				
		call_user_func(array($peerName, 'setDefaultCriteriaFilter'));
		unset(self::$s_filterred_peer_list[$peerName]); 
		unset(self::$partnerCriteriaParams[$objectName]);
	}

	// will reset all the filters used in the applyPartnerFilters
	public static function resetAllFilters()
	{
		foreach ( self::$s_filterred_peer_list as $peerName )
		{
			call_user_func(array($peerName, 'setDefaultCriteriaFilter'));
		}
		
		self::$s_filterred_peer_list = array();
		self::$partnerCriteriaParams = array();
	}
	
	/**
	 * Will set the pertner filter in all 3 peers:
	 * 	kuserPeer
	 * 	kshowPeer
	 * 	entryPeer
	 *
	 * @param unknown_type $partner_id
	 */
	public static function applyPartnerFilters ( $partner_id=null , $private_partner_data = false , $partner_group = null , $kaltura_network = null )
	{
		if ( $partner_id === null )
		{
			$partner_id = self::$s_current_partner_id;
		}

		//Category peer should be added before entry - since we select from category on entry->setDefaultCriteria.
		self::addPartnerToCriteria ( 'kuser', $partner_id , $private_partner_data, $partner_group);
		self::addPartnerToCriteria ( 'category' , $partner_id , $private_partner_data , $partner_group);
		self::addPartnerToCriteria ( 'entry' , $partner_id , $private_partner_data, $partner_group , $kaltura_network );
		self::addPartnerToCriteria ( 'kshow' , $partner_id , $private_partner_data, $partner_group , $kaltura_network );
		self::addPartnerToCriteria ( 'moderation' , $partner_id , $private_partner_data , $partner_group);
		self::addPartnerToCriteria ( 'categoryEntry' , $partner_id , $private_partner_data , $partner_group);
		self::addPartnerToCriteria ( 'categoryKuser', $partner_id , $private_partner_data , $partner_group);
	}

	public static function getPartnerCriteriaParams($objectName)
	{
		$peerName = $objectName . 'Peer';
		
		$objectName = strtolower($objectName);
		if (!isset(self::$partnerCriteriaParams[$objectName]))
			return null;
			
		$result = self::$partnerCriteriaParams[$objectName];
		unset(self::$partnerCriteriaParams[$objectName]);
		self::$s_filterred_peer_list[] = $peerName;
		
		return $result;
	}
	
	// if only partner_id exists - force it on the criteria
	// if also $partner_group - allow or partner_id or the partner_group - use in ( partner_id ,  $partner_group ) - where partner_group is split by ','
	// if partner_group == "*" - don't filter at all
	// if $kaltura_network - add 'or  display_in_search >= 2'
	public static function addPartnerToCriteria ( $objectName, $partner_id, $private_partner_data = false , $partner_group=null , $kaltura_network=null )
	{
		$objectName = strtolower($objectName);
		self::$partnerCriteriaParams[$objectName] = array($partner_id, $private_partner_data, $partner_group, $kaltura_network);
	}


	public static function setPartnerFrocePolicy ( $val )
	{
		self::$s_set_partner_id_policy = $val;
	}

	public static function setPartnerIdForObj ( BaseObject $obj )
	{
		if ( self::$s_set_partner_id_policy == self::PARTNER_SET_POLICY_NONE )
			return;
		if ( $obj == null )
			return;

		$current_obj_partner = $obj->getPartnerId();
		if ( self::$s_set_partner_id_policy == self::PARTNER_SET_POLICY_IF_NULL  && $current_obj_partner == null)
		{
			$obj->setPartnerId ( self::$s_current_partner_id );
			return;
		}

		// force
		$obj->setPartnerId ( self::$s_current_partner_id );
	}


	public static function getMediaServiceProviders ( $partner_id , $subp_id )
	{
		$provider_id_list = myMediaSourceFactory::getAllMediaSourceProvidersIds ();

		$result = array();
		foreach ( $provider_id_list as $provider_id )
		{

			$provider = myMediaSourceFactory::getMediaSource( $provider_id );
			$res = $provider->getSearchConfig();
			$result["__$provider_id" . "_service"] = $res;
		}

		return $result;

	}

	public static function createWidgetImage($partner, $create)
	{
		$contentPath = myContentStorage::getFSContentRootPath();
		$path = kFile::fixPath( $contentPath.$partner->getWidgetImagePath() );

		// if the create flag is not set and the file doesnt exist exit
		// e.g. the roughcut name has change, we update the image only if it was already in some widget
		if (!$create && !file_exists($path))
			return;

		$im = imagecreatetruecolor(400,20);

		$green = imagecolorallocate($im, 188, 230, 99);
		$white = imagecolorallocate($im, 255, 255, 255);

		$font = SF_ROOT_DIR.'/web/ttf/arial.ttf';

		$fontSize = 9;
		$bottom = 15;

		$pos = imagettftext($im, $fontSize, 0, 10, $bottom, $green, $font, $partner->getPartnerName()." Collaborative Video");
		$pos = imagettftext($im, $fontSize, 0, $pos[2], $bottom, $white, $font, " powered by ");
		imagettftext($im, $fontSize, 0, $pos[2], $bottom, $green, $font, "Kaltura");

		kFile::fullMkdir($path);

		imagegif($im, $path);
		imagedestroy($im);

	}

	public static function shouldForceUniqueKshow ( $partner_id , $allow_duplicate_names )
	{
		// TODO - make more generic !
		// TODO - remove this code - it's only for wikis
		if ( ! $allow_duplicate_names ) return true;
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner ) return !$allow_duplicate_names;
		return $partner->getShouldForceUniqueKshow();
	}

	public static function returnDuplicateKshow ( $partner_id )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner ) return false;
		return $partner->getReturnDuplicateKshow();		
	}
	
	public static function shouldNotify ( $partner_id )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner ) return array ( false , null );
		return array ( $partner->getNotify() , $partner->getNotificationsConfig() ) ;
	}

	public static function shouldModerate ( $partner_id, $entry = null )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner ) return false;
		$should_moderate = $partner->getModerateContent();
		if ($should_moderate && !is_null($entry))
		{
			$autoModerateEntryFilter = $partner->getAutoModerateEntryFilter();
			//filter exists and entry doesn't match filter
			if ($autoModerateEntryFilter && !$autoModerateEntryFilter->typeMatches($entry))
			{
				$should_moderate = false;
			}				
		}
		return $should_moderate;		
	}
	
	// if the host of the partner is false or null or an empty string - ignore it
	public static function getHost ( $partner_id )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner || (! $partner->getHost() ) ) return requestUtils::getRequestHost();
		return $partner->getHost();
	}
	
	
	public static function getCdnHost ( $partner_id, $protocol = null )
	{
		// in case the request came through https, force https url
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$protocol = 'https';

		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner || (! $partner->getCdnHost() ) ) return requestUtils::getCdnHost($protocol === null ? 'http' : $protocol);

		$cdnHost = $partner->getCdnHost();

		// temporary default is http since the system is not aligned to use https in all of its components (e.g. kmc)
		// right now, if a partner cdnHost is set to https:// the kmc wont work well if we reply with https prefix to its requests
		if ($protocol === null)
			$protocol='http';

		// if a protocol was set manually (or by the temporary http default above) use it instead of the partner setting
		if ($protocol !== null)
			$cdnHost = preg_replace('/^https?/', $protocol, $cdnHost);
			
		return $cdnHost;
	}
	
	public static function getThumbnailHost ($partner_id, $protocol = null)
	{
	    $partner = PartnerPeer::retrieveByPK( $partner_id );
	    if ( !$partner || (! $partner->getThumbnailHost() ) ) return self::getCdnHost($partner_id, $protocol);
	    
	    // in case the request came through https, force https url
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$protocol = 'https';

		$thumbHost = $partner->getThumbnailHost();

		// temporary default is http since the system is not aligned to use https in all of its components (e.g. kmc)
		// right now, if a partner cdnHost is set to https:// the kmc wont work well if we reply with https prefix to its requests
		if ($protocol === null)
			$protocol='http';

		// if a protocol was set manually (or by the temporary http default above) use it instead of the partner setting
		if ($protocol !== null)
			$thumbHost = preg_replace('/^https?/', $protocol, $thumbHost);
			
		return $thumbHost;
	}
	
	// if the cdnHost of the partner is false or null or an empty string - ignore it	
	public static function getRtmpUrl ( $partner_id )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner || (! $partner->getRtmpUrl() ) ) return requestUtils::getRtmpUrl();
		return $partner->getRtmpUrl();
	}
	
	// if the iis Host of the partner is false or null or an empty string - ignore it	
	public static function getIisHost ( $partner_id, $protocol = 'http' )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner || (! $partner->getIisHost() ) ) return requestUtils::getIisHost($protocol);
		
		$iisHost = $partner->getIisHost();
		$iisHost = preg_replace('/^https?/', $protocol, $iisHost);
		return $iisHost;
	}
	
	
	// TODO - cleanup !!	
	/**
	 * Will determine the conversion string for the entry id.
	 * This depends on the partner and the nature of the file
	 */
	public static function getConversionStringForEntry ( $entry_id , $file_name )
	{
		$entry = entryPeer::retrieveByPK( $entry_id );
		if ( ! $entry ) return null;
		
		$conversion_profile_id = $entry->getConversionQuality();

		$partner = PartnerPeer::retrieveByPK( $entry->getPartnerId() );
		if ( ! $partner ) return null ; // VERY BAD !!

		// check the type of the file
		// if of type flv - check for flvConversionString

		$conversion_str = "";
		
		// prefer the conversion_profile over the conversion_string (which is obsolete)
		if ( ! $conversion_profile_id ) $conversion_profile_id =  $partner->getDefConversionProfileType();
		if ( ! $conversion_profile_id )
		{
			if ( myFlvStaticHandler::isFlv( $file_name ) )
			{ 
				$conversion_str = $partner->getFlvConversionString();
			}
	
			if ( ! $conversion_str ) $conversion_str = $partner->getConversionString();
			/// TODO - optimize - check according to the conversion string if need to fetch data from the file
			list ( $video_width , $video_height ) = myFileConverter::getVideoDimensions( $file_name );
	
			$conversion_str = myFileConverter::formatConversionString ( $conversion_str , $video_width , $video_height );
		}
		
		// if the $conversion_profile_id is not specified on the entry - look at the partner's conversion_string
		// TODO - HACK, this is until we have a default conversion_profile for the partner
		if ( !$conversion_profile_id && strpos ($conversion_str , "!" ) === 0 )
		{
			// the conversion string strart with ! - use this as the default conversionQuality of the partner
			// copy it on the entry - it will follow the entry from this point onwards
			$conversion_profile_id = substr ( $conversion_str , 1 ); // without the !
		}
		
		// update the entry if there is a better $conversion_profile_id than the original one the entry had
		if ( $conversion_profile_id && $conversion_profile_id != $entry->getConversionQuality() )
		{
			$entry->setConversionQuality( $conversion_profile_id  );
			$entry->save();
		}
		
		return array ( $conversion_str , $conversion_profile_id );
	}
	
	/**
	 * return the current ConversionProfile for the partner
	 * This will check if the partner has an explicit conversion profile marked as "current".
	 * If the partner has not got such a profile, the system default is used.
	 */
	public static function getCurrentConversionProfile ( $partner_id  )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( ! $partner ) return null ; // VERY BAD !!		
		
		// will return the partner OLD default profile and if not found - the system default profile
		$conversion_profile_id =  $partner->getCurrentConversionProfileType();
		if ( ! $conversion_profile_id )
			$conversion_profile_id =  $partner->getDefConversionProfileType();
		
		return myConversionProfileUtils::getConversionProfile( $partner_id , $conversion_profile_id );
	}
	
	// TODO - cleanup !!
		/**
	 * return the ConversionProfile for this entry is specified on the entry or for the partner
	 * the is_flv is important for deciding on the actual set of conversion params
	 */
	public static function getConversionProfileForEntry ( $entry_id  )
	{
		$entry = entryPeer::retrieveByPK( $entry_id );
		if ( ! $entry ) return null;
		
		// conversion quality is an alias for conersion_profile_type ('low' , 'med' , 'hi' , 'hd' ... )
		$conversion_profile_quality = $entry->getConversionQuality();
		
		// TODO - change the const ConversionProfile::CONVERSION_PROFILE_UNKNOWN
		if ( $conversion_profile_quality && $conversion_profile_quality != ConversionProfile::CONVERSION_PROFILE_UNKNOWN )
		{
			$partner_id = $entry->getPartnerId();	
		}
		else
		{
			$partner = PartnerPeer::retrieveByPK( $entry->getPartnerId() );
			if ( ! $partner ) return null ; // VERY BAD !!
			
			$partner_id = $partner->getId();
			
			$conversion_profile_quality = $partner->getCurrentConversionProfileType();
			if ( ! $conversion_profile_quality )		// no current - use default
				$conversion_profile_quality = $partner->getDefConversionProfileType();
		}
		
		return myConversionProfileUtils::getConversionProfile ( $partner_id , $conversion_profile_quality  );
	}	
	
	
	/**
	 * return the ConversionProfile for this entry is specified on the entry or for the partner
	 * the is_flv is important for deciding on the actual set of conversion params
	 * 
	 * @param string $entry_id
	 * @return conversionProfile2
	 */
	public static function getConversionProfile2ForEntry ( $entry_id  )
	{
		$entry = entryPeer::retrieveByPK( $entry_id );
		if ( ! $entry ) return null;
		
		// conversion quality is an alias for conersion_profile_type ('low' , 'med' , 'hi' , 'hd' ... )
		$conversion_profile_2_id = $entry->getConversionProfileId();
		$conversion_quality = "";
		
		KalturaLog::log("conversion_profile_2_id [$conversion_profile_2_id]");
		if ( is_null($conversion_profile_2_id) || $conversion_profile_2_id <= 0 )
		{
			// this is assumed to be the old conversion profile
			$conversion_quality = $entry->getConversionQuality();
			$partner_id = $entry->getPartnerId();
	
			// try to extract the conversion profile from the partner
			$partner = PartnerPeer::retrieveByPK( $partner_id );
			if ( ! $partner ) 
			{
				throw new Exception ( "Cannot find partner for entry [$entry_id]" );
			}
			
			KalturaLog::log("conversion_quality [$conversion_quality]");
			$partner_kmc_version = $partner->getKmcVersion ( );
			if ( is_null($partner_kmc_version ) || version_compare( $partner_kmc_version , "2" , "<" ) ) 
			{
				// if old kmc - the fallback conversion_quality is the one on the partner->getDefConversionProfileType
				if ( is_null($conversion_quality) || $conversion_quality <= 0 )
				{
					// search for the default one on the partner
					$old_conversion_profile = self::getCurrentConversionProfile($partner->getId());
				}
				else
				{
					// load old profile from the given conversion_quality
					$old_conversion_profile = myConversionProfileUtils::getConversionProfile ( $partner_id , $conversion_quality  );
				}

				if(!$old_conversion_profile)
				{
					throw new Exception ( "Cannot find conversion profile for entry_id [$entry_id] OLD conversion_quality [$conversion_quality]" );
				}
				
				// this is a partner working with OLD KMC
				// - we need to create the new conversion profile from the old one
				$new_conversion_profile = myConversionProfileUtils::createConversionProfile2FromConversionProfile ( $old_conversion_profile );  
			}
			else
			{
				// if new kmc_version - the fallback conversion_quality is the one on the partner->getDefaultConversionProfileId
	            if ( is_null($conversion_quality) || $conversion_quality <= 0 )
				{
					// search for the default one on the partner
					$conversion_quality = $partner->getDefaultConversionProfileId();
				}


				// partner with new KMC version - use the $conversion_quality as if it was the conversionProfile2 id
				$new_conversion_profile = conversionProfile2Peer::retrieveByPk ( $conversion_quality );
			}
			
			// set the new conversionProfileId on the entry
			if($new_conversion_profile)
			{
				$entry->setConversionProfileId ( $new_conversion_profile->getId() ); 
				$entry->save();
			}
		}
		else
		{
			$new_conversion_profile = conversionProfile2Peer::retrieveByPk ( $conversion_profile_2_id );
		}
		
		// if there is no new conversion_profile OR the new_conversion_profile doesn't belong to the current partner (and doesn't belong to partner 0 - the partner all default profiules belong to)
		// - this indicates an error !
		if ( !$new_conversion_profile || 
			( $new_conversion_profile->getPartnerId() != $entry->getPartnerId() && $new_conversion_profile->getPartnerId() != 0 ) )
		{
			throw new Exception ( "Cannot find conversion profile for entry_id [$entry_id] OLD conversion_quality [$conversion_quality] NEW conversion_profile_2_id [$conversion_profile_2_id]" ); 
		}
		return $new_conversion_profile;		
	}	
	
	/**
	 * @param int $partner_id
	 * @return conversionProfile2
	 */
	public static function getConversionProfile2ForPartner($partner_id, $conversionProfile2Id = null)
	{
		if($conversionProfile2Id == conversionProfile2::CONVERSION_PROFILE_NONE)
			return null;
			
		if(!$conversionProfile2Id)
		{
			// try to extract the conversion profile from the partner
			$partner = PartnerPeer::retrieveByPK($partner_id);
			if(!$partner) 
				throw new Exception("Cannot find partner for id [$partner_id]");
			
			$partner_kmc_version = $partner->getKmcVersion();
			if(is_null($partner_kmc_version) || version_compare($partner_kmc_version, "2", "<")) 
			{
				$old_conversion_profile = self::getCurrentConversionProfile($partner->getId());
				if(!$old_conversion_profile)
					throw new Exception("Cannot find conversion profile for partner id [$partner_id]");
				
				return myConversionProfileUtils::createConversionProfile2FromConversionProfile($old_conversion_profile);  
			}
			
			$conversionProfile2Id = $partner->getDefaultConversionProfileId();
		}
		
		return conversionProfile2Peer::retrieveByPk($conversionProfile2Id);
	}	
	
/*@Partner $partner*/
	public static function getDefaultKshow ( $partner_id, $subp_id , $puser_kuser, $group_id = null , $create_anyway = false, $default_name = null )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id ); 
		if ( !$partner ) return null;
		
		// see if partner allows a fallback kshow
		$allow = $partner->getUseDefaultKshow();
		if ( ! $allow ) return null;

		$kshow = myKshowUtils::getDefaultKshow ( $partner_id , $subp_id , $puser_kuser , $group_id , $partner->getAllowQuickEdit() , $create_anyway , $default_name );
		return $kshow;
	}
	
	
	public static function allowPartnerAccessPartner ( $operating_partner_id , $partner_group , $partner_id )
	{
		// $operating_partner_id is operating on itself
		if ( $operating_partner_id == $partner_id ) return true;
		
		// operating_partner has permission to operate on all partners
		if ( $partner_group == self::ALL_PARTNERS_WILD_CHAR ) return true;

		$partner_group_arr = explode ( "," , $partner_group );
		foreach ( $partner_group_arr as &$single_partner_id ) { $single_partner_id = trim($single_partner_id); } // clear whitespaces

		// ok if the partner_id is explicitly in the partner_group
		return in_array ($partner_id , $partner_group_arr );
	}
	
	public static function getPartnerToken ( $token_prefix , $partner_id , $subp_id , $key )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner ) return null;
		
		$input = $partner_id;

	    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
	    $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	    mcrypt_generic_init($td, $key, $iv);
	    $encrypted_data = mcrypt_generic($td, $input);
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
		
		return $token_prefix . base64_encode( $encrypted_data );
	}
	
	public static function getServiceConfig ( $partner )
	{
		if ( $partner == null )
		{
			return new myServiceConfig( null );
		}
		else
		{
			return $partner->getServiceConfig();
		}
	}
	
	public static function getPartnerUsage( $partner )
	{
		/* should not be called anymore... */
		return;
	
		$c = new Criteria();
		$c->addAnd ( PartnerActivityPeer::ACTIVITY , PartnerActivity::PARTNER_ACTIVITY_STORAGE );
		$c->addAnd ( PartnerActivityPeer::SUB_ACTIVITY , PartnerActivity::PARTNER_SUB_ACTIVITY_STORAGE_SIZE );
		$c->addAnd ( PartnerActivityPeer::PARTNER_ID , $partner->getId() );
    	$c->addSelectColumn('sum('.PartnerActivityPeer::AMOUNT.') as total_hosting');

		$activity = PartnerActivityPeer::doSelectStmt( $c );
	
		$res = $activity->fetchAll();
		foreach($res as $record) 
		{
			$total_hosting = $record[0];
		}
		
//		// old code from doSelectRs
//		while ($activity->next()) { $total_hosting = $activity->get(1); }

		if ( !$total_hosting ) $total_hosting = 0;
		
		$c = new Criteria();
		$c->addAnd ( PartnerActivityPeer::ACTIVITY , PartnerActivity::PARTNER_ACTIVITY_TRAFFIC );
		$c->addAnd ( PartnerActivityPeer::SUB_ACTIVITY , 
			array( 	PartnerActivity::PARTNER_SUB_ACTIVITY_WWW,
					PartnerActivity::PARTNER_SUB_ACTIVITY_LIMELIGHT ),
			Criteria::IN );
		$c->addAnd ( PartnerActivityPeer::PARTNER_ID , $partner->getId() );
		
		switch ( $partner->getPackageClassOfServiceDetails() ){
			case PartnerPackages::CLASS_OF_SERVICE_SILVER:
			case PartnerPackages::CLASS_OF_SERVICE_GOLD:
			case PartnerPackages::CLASS_OF_SERVICE_PLATINUM:
				$time_diff = time()-(60*60*24*((int)date('d')-1));
				$month = date('Y-m-d',$time_diff);
				$c->addAnd ( PartnerActivityPeer::ACTIVITY_DATE, $month, Criteria::GREATER_EQUAL );
				$c->addAnd ( PartnerActivityPeer::ACTIVITY_DATE, date('Y-m-d'), Criteria::LESS_EQUAL  );
				break;
			case PartnerPackages::PARTNER_PACKAGE_FREE:
			default:
				break;
		}
		$packages = new PartnerPackages();
		$partnerPackage = $packages->getPackageDetails($partner->getPartnerPackage());
		
    	$c->addSelectColumn('sum('.PartnerActivityPeer::AMOUNT.') as total_traffic');

		$activity = PartnerActivityPeer::doSelectStmt( $c );
	
		$res = $activity->fetchAll();
		foreach($res as $record) 
		{
			$total_traffic = $record[0];
		}
		
//		// old code from doSelectRs
//		while ($activity->next()) { $total_traffic = $activity->get(1); }
		if ( !$total_traffic ) $total_traffic = 0;
		
		$total = $total_traffic + $total_hosting;
		$return['MB'] = round($total/1024);
		$return['hostingMB'] = round($total_hosting/1024);
		$GB = round($return['MB']/1024, 2);
		$return['Percent'] = (round(($GB/$partnerPackage['cycle_bw']*100), 2))*100;
		$return['package_bw'] = $partnerPackage['cycle_bw'];
		$return['debug']['total'] = $total;
		$return['debug']['total_traffic'] = $total_traffic;
		$return['debug']['total_hosting'] = $total_hosting;

		return $return;
	}
	
	const KALTURA_ACCOUNT_UPGRADES_NOTIFICATION_EMAIL = 'upgrade@kaltura.com';
	public static function notifiyPartner($mail_type, $partner, $body_params = array() )
	{
		/* --- while deploying - do not notifiy the partner, only send internal notifications. --- */
		$body_params[0] = $body_params[0].' (PartnerID: '. $partner->getId() .')';
		kJobsManager::addMailJob(
			null, 
			0, 
			$partner->getId(), 
			$mail_type, 
			kMailJobData::MAIL_PRIORITY_NORMAL, 
			kConf::get ("partner_notification_email" ), 
			kConf::get ("partner_notification_name" ), 
			$partner->getAdminEmail(), 
			$body_params);		

		// add PID,PartnerName,PartnerType to admin name when sending internal notification
		$body_params[0] = $body_params[0].' ('. $partner->getId() .')'." type:[{$partner->getType()}] partnerName:[{$partner->getPartnerName()}]";
		 
		kJobsManager::addMailJob(
			null, 
			0, 
			$partner->getId(), 
			$mail_type, 
			kMailJobData::MAIL_PRIORITY_NORMAL, 
			kConf::get ("partner_notification_email" ), 
			kConf::get ("partner_notification_name" ), 
			myPartnerUtils::KALTURA_ACCOUNT_UPGRADES_NOTIFICATION_EMAIL, 
			$body_params);
	}
	

	public static function emailChangedEmail($partner_id, $partner_old_email, $partner_new_email, $partner_name , $mail_type )
	{
		kJobsManager::addMailJob(
			null, 
			0, 
			$partner_id, 
			$mail_type, 
			kMailJobData::MAIL_PRIORITY_NORMAL, 
			kConf::get ("partner_change_email_email" ), 
			kConf::get ("partner_change_email_name" ), 
			$partner_old_email.','.$partner_new_email, 
			array($partner_name,$partner_old_email,$partner_new_email));
	}
		
	const KALTURA_PACKAGE_EIGHTY_PERCENT_WARNING = 81;
	const KALTURA_PACKAGE_LIMIT_WARNING_1 = 82;
	const KALTURA_PACKAGE_LIMIT_WARNING_2 = 83;
	const KALTURA_DELETE_ACCOUNT = 84;
	const KALTURA_PAID_PACKAGE_SUGGEST_UPGRADE = 85;
	const KALTURA_EXTENED_FREE_TRAIL_ENDS_WARNING = 87;
	
	const IS_FREE_PACKAGE_PLACE_HOLDER = "{IS_FREE_PACKAGE}";
	
	
	public static function collectPartnerStatisticsFromDWH($partner, $partnerPackage, $report_date, $data_for_graph = false)
    {
        // reset values:
        $totalStorage = 0;
        $totalTraffic = 0;
        $totalUsage = 0;

		$reportFilter = new reportsInputFilter();
		$reportFilter->from_day = str_replace('-','',$report_date);

		$reportFilter->extra_map[self::IS_FREE_PACKAGE_PLACE_HOLDER] = "FALSE";
		if ($partnerPackage['id'] == 1) // free package
			$reportFilter->extra_map[self::IS_FREE_PACKAGE_PLACE_HOLDER] = "TRUE";
		
		list($header, $data) = myReportsMgr::getTable( $partner->getId(), myReportsMgr::REPORT_TYPE_PARTNER_USAGE_DASHBOARD ,
		 $reportFilter, 10000 , 1 , "", null);

		$avg_continuous_aggr_storage_mb_key = array_search('avg_continuous_aggr_storage_mb', $header);
		$sum_partner_bandwidth_kb_key = array_search('sum_partner_bandwidth_kb', $header);
		
        $relevant_row = count($data)-1;
          
		$totalStorage = $data[$relevant_row][$avg_continuous_aggr_storage_mb_key]; // MB
        $totalTraffic = $data[$relevant_row][$sum_partner_bandwidth_kb_key]; // KB
        $totalUsage = ($totalStorage*1024) + $totalTraffic; // (MB*1024 => KB) + KB

        return array( $totalStorage , $totalUsage , $totalTraffic );
    }
    
    /**
	 * deprecated - use collectPartnerStatisticsFromDWH instead 
	 */
	public static function collectPartnerUsageFromDWH($partner, $partnerPackage, $report_date, $data_for_graph = false)
    {
    	// reset values:
        $totalStorage = 0;
        $totalTraffic = 0;
        $totalUsage = 0;

		$reportFilter = new reportsInputFilter();
		$reportFilter->from_day = str_replace('-','',$report_date);

		list($header, $data) = myReportsMgr::getTable( $partner->getId(), myReportsMgr::REPORT_TYPE_PARTNER_BANDWIDTH_USAGE ,
		 $reportFilter, 10000 , 1 , "", null);

		$avg_continuous_aggr_storage_mb_key = array_search('avg_continuous_aggr_storage_mb', $header);
		$sum_partner_bandwidth_kb_key = array_search('sum_partner_bandwidth_kb', $header);
		
        // according to $partnerPackage['id'], decide which row to take (last date, or full rollup row)
        if ($partnerPackage['id'] == 1) // free package
        {
		    // $res[count($res)-1] => specific partner rollup, relevant for free partner
            $relevant_row = count($data)-1;
        }
        else
        {
		    // $res[count($res)-1] => specific partner rollup, relevant for free partner
		    // $res[count($res)-2] => specific partner, last month, relevant for paying partner
            $relevant_row = count($data)-2;
        }
          
        $totalStorage = $data[$relevant_row][$avg_continuous_aggr_storage_mb_key]; // MB
        $totalTraffic = $data[$relevant_row][$sum_partner_bandwidth_kb_key]; // KB
        $totalUsage = ($totalStorage*1024) + $totalTraffic; // (MB*1024 => KB) + KB

        return array( $totalStorage , $totalUsage , $totalTraffic );
    }
            
	/**
	 * deprecated - data moved to DWH
	 */
	public static function collectPartnerUsage($partner, $partnerPackage, $report_date)
	{
		$c = new Criteria();
		$c->addAnd(PartnerActivityPeer::ACTIVITY, PartnerActivity::PARTNER_ACTIVITY_MONTHLY_AGGREGATION);
		$c->addAnd(PartnerActivityPeer::PARTNER_ID, $partner->getId());
		if ($partnerPackage['id'] != 1) // free package
		{
			$c->addAnd(PartnerActivityPeer::ACTIVITY_DATE, $report_date);
		}
		$activities = PartnerActivityPeer::doSelect($c);
		
		$totalUsage   = 0;
		$totalStorage = 0;
		$totalTraffic = 0;
		if ($partnerPackage['id'] == 1) // free package
		{
			foreach($activities as $activity)
			{
				$totalStorage += $activity->getAmount1();
				$totalTraffic += $activity->getAmount2();
				$totalUsage   += $activity->getAmount();
			}
		}
		else
		{
			if (count($activities))
			{
				$totalStorage = $activities[0]->getAmount1(); // MB value
				$totalTraffic = $activities[0]->getAmount2(); // KB value
				$totalUsage   = $activities[0]->getAmount(); // KB value
			}
			else
			{
				$totalStorage = $totalTraffic = $totalUsage = 0;
				// probably 04/Month/year, and batch didn't get to that partner yet
			}
		}
		return array( $totalStorage , $totalUsage , $totalTraffic );
	}
	
	public static function getEmailLinkHash($partner_id, $partner_secret)
	{
		return md5($partner_secret.$partner_id.kConf::get('kaltura_email_hash'));
	}
	
	public static function doPartnerUsage(Partner $partner)
	{
		KalturaLog::debug("Validating partner [" . $partner->getId() . "]");
		if($partner->getExtendedFreeTrail())
		{
			KalturaLog::debug("Partner [" . $partner->getId() . "] trial account has extension");
			if($partner->getExtendedFreeTrailExpiryDate() < time())
			{
				//ExtendedFreeTrail ended
				$partner->setExtendedFreeTrail(null);
				$partner->setExtendedFreeTrailExpiryDate(null);
				$partner->setExtendedFreeTrailExpiryReason('');
			}else{
				//ExtendedFreeTrail
				if (($partner->getExtendedFreeTrailExpiryDate() < (time() + (dateUtils::DAY * 7))) && !$partner->getExtendedFreeTrailEndsWarning())
				{
					$partner->setExtendedFreeTrailEndsWarning(true);
					$partner->save();
					$email_link_hash = 'pid='.$partner->getId().'&h='.(self::getEmailLinkHash($partner->getId(), $partner->getSecret()));
					$mail_parmas = array($partner->getAdminName() ,$email_link_hash);
					myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_EXTENED_FREE_TRAIL_ENDS_WARNING, $partner, $mail_parmas);
				}			
				
				KalturaLog::debug("Partner [" . $partner->getId() . "] trial account extended");
				return;
			}
			
		}
		
		$should_block_delete_partner = true;
		
		$blocking_days_grace = 7;
		$block_notification_grace = time() - (dateUtils::DAY * $blocking_days_grace);
		$delete_grace = time() -  (dateUtils::DAY * 30);
		
		$packages = new PartnerPackages();
		$partnerPackage = $packages->getPackageDetails($partner->getPartnerPackage());
		
		$report_date = date('Y-m').'-01';
        // We are now working with the DWH and a stored-procedure, and not with record type 6 on partner_activity.
        $report_date = dateUtils::todayOffset(-1);

		list ( $totalStorage , $totalUsage , $totalTraffic ) = myPartnerUtils::collectPartnerStatisticsFromDWH($partner, $partnerPackage, $report_date);
		$totalUsageGB = $totalUsage/1024/1024; // from KB to GB
		$percent = round( ($totalUsageGB / $partnerPackage['cycle_bw'])*100, 2);

		KalturaLog::debug("percent (".$partner->getId().") is: $percent");
		$email_link_hash = 'pid='.$partner->getId().'&h='.(self::getEmailLinkHash($partner->getId(), $partner->getSecret()));
		$email_link_hash_adOpt = $email_link_hash.'&type=adOptIn';
		/* mindtouch partners - extra mail parameter */
		$mindtouch_notice = ' ';
		if($partner->getType() == 103) // dekiwiki-mindtouch partner
		{
			$mindtouch_notice = '<BR><BR>Note: You must be a MindTouch paying customer to upgrade your video service. If you are not a paying MindTouch customer, contact MindTouch: http://www.mindtouch.com/about_mindtouch/contact_mindtouch to get a quote.<BR><BR>';
		}
		if ($percent >= 80 &&
			$percent < 100 &&
			!$partner->getEightyPercentWarning())
		{
			KalturaLog::debug("partner ". $partner->getId() ." reached 80% - setting first warning");
				
			/* prepare mail job, and set EightyPercentWarning() to true/date */
			$partner->setEightyPercentWarning(time());
			$partner->setUsageLimitWarning(0);
			$body_params = array ( $partner->getAdminName(), $partnerPackage['cycle_bw'], $mindtouch_notice, round($totalUsageGB, 2), $email_link_hash );
			myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_PACKAGE_EIGHTY_PERCENT_WARNING, $partner, $body_params);
		}
		elseif ($percent >= 80 &&
			$percent < 100 &&
			$partner->getEightyPercentWarning() &&
			!$partner->getUsageLimitWarning())
		{
			KalturaLog::debug("passed the 80%, assume notification sent, nothing to do.");
		}
		elseif ($percent < 80 &&
				$partner->getEightyPercentWarning())
		{
			KalturaLog::debug("partner ". $partner->getId() ." was 80%, now not. clearing warnings");
				
			/* clear getEightyPercentWarning */
			$partner->setEightyPercentWarning(0);
			$partner->setUsageLimitWarning(0);
		}
		elseif ($percent >= 100 &&
				!$partner->getUsageLimitWarning())
		{
			KalturaLog::debug("partner ". $partner->getId() ." reached 100% - setting second warning");
				
			/* prepare mail job, and set getUsageLimitWarning() date */
			$partner->setUsageLimitWarning(time());
			// if ($partnerPackage['cycle_fee'] == 0) - script always works on free partners anyway
			{
				$body_params = array ( $partner->getAdminName(), $mindtouch_notice, round($totalUsageGB, 2), $email_link_hash );
				myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_PACKAGE_LIMIT_WARNING_1, $partner, $body_params);
			}
		}
		elseif ($percent >= 100 &&
				$partnerPackage['cycle_fee'] == 0 &&
				$partner->getUsageLimitWarning() > 0 && 
				$partner->getUsageLimitWarning() <= $block_notification_grace &&
				$partner->getUsageLimitWarning() > $delete_grace &&
				$partner->getStatus() != Partner::PARTNER_STATUS_CONTENT_BLOCK)
		{
			KalturaLog::debug("partner ". $partner->getId() ." reached 100% $blocking_days_grace days ago - sending block email and blocking partner");
				
			/* send block email and block partner */
			$body_params = array ( $partner->getAdminName(), $mindtouch_notice, round($totalUsageGB, 2), $email_link_hash );
			myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_PACKAGE_LIMIT_WARNING_2, $partner, $body_params);
			if($should_block_delete_partner)
			{
				$partner->setStatus(Partner::PARTNER_STATUS_CONTENT_BLOCK);
			}
		}
		elseif ($percent >= 120 &&
				$partnerPackage['cycle_fee'] != 0 &&
				$partner->getUsageLimitWarning() <= $block_notification_grace)
		{
			$body_params = array ( $partner->getAdminName(), round($totalUsageGB, 2) );
			myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_PAID_PACKAGE_SUGGEST_UPGRADE, $partner, $body_params);
		}
		elseif ($percent >= 100 &&
				$partnerPackage['cycle_fee'] == 0 &&
				$partner->getUsageLimitWarning() > 0 &&
				$partner->getUsageLimitWarning() <= $delete_grace &&
				$partner->getStatus() == Partner::PARTNER_STATUS_CONTENT_BLOCK)
		{
			KalturaLog::debug("partner ". $partner->getId() ." reached 100% a month ago - deleting partner");
				
			/* delete partner */
			$body_params = array ( $partner->getAdminName() );
			myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_DELETE_ACCOUNT, $partner, $body_params);
			if($should_block_delete_partner)
			{
				$partner->setStatus(Partner::PARTNER_STATUS_DELETED);
			}
		}
		elseif($percent < 80 && ($partner->getUsageLimitWarning() || $partner->getEightyPercentWarning()))
		{
			KalturaLog::debug("partner ". $partner->getId() ." OK");
			// PARTNER OK 
			// resetting status and warnings should only be done manually
			//$partner->setStatus(1);
			$partner->setEightyPercentWarning(0);
			$partner->setUsageLimitWarning(0);
			
		}
		$partner->save();		
	}
		
	public static function getParnerWidgetStatisticsFromDWH($partnerId, $startDate, $endDate) {
		$reportFilter = new reportsInputFilter();
		
		// use gmmktime to avoid server timezone offset - this is for backward compatibility while the KMC is not sending TZ info
		list($year, $month, $day) = explode('-', $startDate);
		$reportFilter->from_date = gmmktime(0, 0, 0, $month, $day, $year);
		$reportFilter->from_day = str_replace('-','',$startDate);
		list($year, $month, $day) = explode('-', $endDate);
		$reportFilter->to_date = gmmktime(0, 0, 0, $month, $day, $year);
		$reportFilter->to_day = str_replace('-','',$endDate);
		$res = myReportsMgr::getGraph ( $partnerId , myReportsMgr::REPORT_TYPE_WIDGETS_STATS , $reportFilter , null , null );
		return $res;
	}
	
	/**
	 * @param int $startDate
	 * @param int $endDate
	 * @param Partner $partner
	 * @param reportInterval $resolution
	 * @param int $tzOffset
	 * @return string
	 */
	public static function getPartnerUsageGraph($startDate, $endDate, Partner $partner, $resolution = reportInterval::DAYS, $tzOffset = null, $reportType = myReportsMgr::REPORT_TYPE_PARTNER_BANDWIDTH_USAGE)
	{
		$reportFilter = new reportsInputFilter();
		
		$reportFilter->from_date = $startDate;
		$reportFilter->to_date = $endDate;
		$reportFilter->from_day = date ( "Ymd" , $startDate );
		$reportFilter->to_day = date ( "Ymd" , $endDate );	
		
		$reportFilter->interval = $resolution;
		
		// if TZ offset provided, add TZ offset to the UTC time created above to reflect the user's timezone
		// in myReportsMgr the offset will be later cleaned again to reflect UTC time so that the DWH query will be correct (with the TIME_SHIFT)
		if(!is_null($tzOffset))
		{
			$tzOffsetSec = $tzOffset * 60;
			$reportFilter->timeZoneOffset = $tzOffsetSec;
			$reportFilter->from_date = $reportFilter->from_date + $tzOffsetSec;
			$reportFilter->to_date = $reportFilter->to_date + $tzOffsetSec;
		}
		
		$data = myReportsMgr::getGraph($partner->getId(), $reportType, $reportFilter, null, null);
		
		$graphPointsLine = array();
		if($resolution == reportInterval::MONTHS)
		{
			$graphPointsLine = myPartnerUtils::annualActivityGraph($data);
		}
		else
		{
			$graphPointsLine = myPartnerUtils::dailyActivityGraph($data, $startDate);
		}

		ksort($graphPointsLine);		
		$graphLine = '';
		foreach($graphPointsLine as $point => $usage)
		{
			$graphLine .= intval($point) . ",$usage;";
		}
		
		return $graphLine;
	}
	
	protected static function annualActivityGraph($data)
	{
		$points = array_fill(1, 12, 0);
		
		if(!isset($data['bandwidth'])) 
			return $points;
			
		foreach($data['bandwidth'] as $monthId => $bandwidth)
		{
			$year = floor($monthId / 100);
			$month = $monthId - ($year * 100);
			$points[intval($month)] = round($bandwidth / 1024); // bandwidth info returned from DWH is in KB, converting to MB
		}

		return $points;
	}		
	
	protected static function dailyActivityGraph($data, $startDate)
	{
		$daysInMonth = date('t', (int)strtotime($startDate));
		$points = array_fill(1, $daysInMonth, 0);
		
		if(!isset($data['bandwidth'])) 
			return $points;
			
		foreach ($data['bandwidth'] as $dateId => $bandwidth)
		{
			$day = $dateId % 100;
			$points[$day] += round($bandwidth); // normalize to MB
		}
		
		return $points;
 	}
 	
 	public static function copyTemplateContent(Partner $fromPartner, Partner $toPartner, $dontCopyUsers = false)
 	{
 		$partnerCustomDataArray = $fromPartner->getCustomDataObj()->toArray();
 		$excludeCustomDataFields = kConf::get('template_partner_custom_data_exclude_fields');
 		foreach($partnerCustomDataArray as $customDataName => $customDataValue)
 			if(!in_array($customDataName, $excludeCustomDataFields))
 				$toPartner->putInCustomData($customDataName, $customDataValue);
		
		// copy permssions before trying to copy additional objects such as distribution profiles which are not enabled yet for the partner
 		self::copyPermissions($fromPartner, $toPartner);
		self::copyUserRoles($fromPartner, $toPartner);
 		
		kEventsManager::raiseEvent(new kObjectCopiedEvent($fromPartner, $toPartner));
 		
 		self::copyAccessControls($fromPartner, $toPartner);
 		self::copyFlavorParams($fromPartner, $toPartner);
 		self::copyConversionProfiles($fromPartner, $toPartner);
		
		categoryEntryPeer::setUseCriteriaFilter(false);
 		self::copyCategories($fromPartner, $toPartner);
 		self::copyEntriesByType($fromPartner, $toPartner, entryType::MEDIA_CLIP, $dontCopyUsers);
 		self::copyEntriesByType($fromPartner, $toPartner, entryType::PLAYLIST, $dontCopyUsers);
		categoryEntryPeer::setUseCriteriaFilter(true);
 		
 		self::copyUiConfsByType($fromPartner, $toPartner, uiConf::UI_CONF_TYPE_WIDGET);
 		self::copyUiConfsByType($fromPartner, $toPartner, uiConf::UI_CONF_TYPE_KDP3);
 	}
 	
	public static function copyUserRoles(Partner $fromPartner, Partner $toPartner)
 	{
 		KalturaLog::log('Copying user roles from partner ['.$fromPartner->getId().'] to partner ['.$toPartner->getId().']');
 		UserRolePeer::setUseCriteriaFilter ( false );
 		$c = new Criteria();
 		$c->addAnd(UserRolePeer::PARTNER_ID, $fromPartner->getId(), Criteria::EQUAL);
 		$c->addDescendingOrderByColumn(UserRolePeer::CREATED_AT);
 		
 		$roles = UserRolePeer::doSelect($c);
 		UserRolePeer::setUseCriteriaFilter ( true );
 		foreach($roles as $role)
 		{
 			$newRole = $role->copyToPartner($toPartner->getId());
 			$newRole->save();
 		}
 	}
 	
	public static function copyPermissions(Partner $fromPartner, Partner $toPartner)
 	{
 		KalturaLog::log('Copying permissions from partner ['.$fromPartner->getId().'] to partner ['.$toPartner->getId().']');
 		PermissionPeer::setUseCriteriaFilter ( false );
 		$c = new Criteria();
 		$c->addAnd(PermissionPeer::PARTNER_ID, $fromPartner->getId(), Criteria::EQUAL);
 		$c->addDescendingOrderByColumn(PermissionPeer::CREATED_AT);
 		$permissions = PermissionPeer::doSelect($c);
 		PermissionPeer::setUseCriteriaFilter ( true );
 		foreach($permissions as $permission)
 		{
 			$newPermission = $permission->copyToPartner($toPartner->getId());
 			$newPermission->save();
 		}
 	}
 	
 	public static function copyCategories(Partner $fromPartner, Partner $toPartner)
 	{
 		KalturaLog::log("Copying categories from partner [".$fromPartner->getId()."] to partner [".$toPartner->getId()."]");
 		
 		categoryPeer::setUseCriteriaFilter(false);
 		$c = new Criteria();
 		$c->addAnd(categoryPeer::PARTNER_ID, $fromPartner->getId());
 		$c->addAnd(categoryPeer::STATUS, CategoryStatus::ACTIVE);
 		$c->addAscendingOrderByColumn(categoryPeer::DEPTH);
 		$c->addAscendingOrderByColumn(categoryPeer::CREATED_AT);
 		
 		$categories = categoryPeer::doSelect($c);
 		categoryPeer::setUseCriteriaFilter(true);
 		
 		foreach($categories as $category)
 		{
 			/* @var $category category */
 			$newCategory= $category->copy();
 			$newCategory->setPartnerId($toPartner->getId());
 			if($category->getParentId())
 				$newCategory->setParentId(kObjectCopyHandler::getMappedId('category', $category->getParentId()));
 				
 			$newCategory->save();
 			
			$newCategory->setIsIndex(true);
 			categoryPeer::setUseCriteriaFilter(false);
			$newCategory->reSetFullIds();
			$newCategory->reSetInheritedParentId();
			$newCategory->reSetDepth();
			$newCategory->reSetFullName();
 			categoryPeer::setUseCriteriaFilter(true);
			
			$newCategory->setEntriesCount(0);
			$newCategory->setMembersCount(0);
			$newCategory->setPendingMembersCount(0);
			$newCategory->setDirectSubCategoriesCount(0);
			$newCategory->setDirectEntriesCount(0);
			$newCategory->save();
 			
 			KalturaLog::log("Copied [".$category->getId()."], new id is [".$newCategory->getId()."]");
 		}
 	}
 	
 	public static function copyEntriesByType(Partner $fromPartner, Partner $toPartner, $entryType, $dontCopyUsers = false)
 	{
 		KalturaLog::log("Copying entries from partner [".$fromPartner->getId()."] to partner [".$toPartner->getId()."] with type [".$entryType."]");
 		entryPeer::setUseCriteriaFilter ( false );
 		$c = new Criteria();
 		$c->addAnd(entryPeer::PARTNER_ID, $fromPartner->getId());
 		$c->addAnd(entryPeer::TYPE, $entryType);
 		$c->addAnd(entryPeer::STATUS, entryStatus::READY);
 		$c->addDescendingOrderByColumn(entryPeer::CREATED_AT);
 		
 		$entries = entryPeer::doSelect($c);
 		entryPeer::setUseCriteriaFilter ( true );
 		foreach($entries as $entry)
 		{
 			myEntryUtils::copyEntry($entry, $toPartner, $dontCopyUsers);
 		}
 	}
 	
 	public static function copyUiConfsByType(Partner $fromPartner, Partner $toPartner, $uiConfType)
 	{
 		KalturaLog::log("Copying uiconfs from partner [".$fromPartner->getId()."] to partner [".$toPartner->getId()."] with type [".$uiConfType."]");
 		uiConfPeer::setUseCriteriaFilter(false);
 		$c = new Criteria();
 		$c->addAnd(uiConfPeer::PARTNER_ID, $fromPartner->getId());
 		$c->addAnd(uiConfPeer::OBJ_TYPE, $uiConfType);
 		$c->addAnd(uiConfPeer::STATUS, uiConf::UI_CONF_STATUS_READY);
 		
 		$uiConfs = uiConfPeer::doSelect($c);
 		uiConfPeer::setUseCriteriaFilter ( true );
 		foreach($uiConfs as $uiConf)
 		{
			// create a new uiConf, set its partner Id (so that upcoming file_sync will have the new partner's id)
			// and clone fileds from current uiConf
 			$newUiConf = new uiConf();
 			$newUiConf->setPartnerId($toPartner->getId());
 			$newUiConf = $uiConf->cloneToNew($newUiConf);
 			$newUiConf->save();
 			
 			KalturaLog::log("UIConf [".$newUiConf->getId()."] was created");
 		}
 	}
 	
 	public static function copyFlavorParams(Partner $fromPartner, Partner $toPartner)
 	{
 		KalturaLog::log("Copying flavor params from partner [".$fromPartner->getId()."] to partner [".$toPartner->getId()."]");
 		
 		$c = new Criteria();
 		$c->add(assetParamsPeer::PARTNER_ID, $fromPartner->getId());
 		
 		$flavorParamsObjects = assetParamsPeer::doSelect($c);
 		foreach($flavorParamsObjects as $flavorParams)
 		{
 			$newFlavorParams = $flavorParams->copy();
 			$newFlavorParams->setPartnerId($toPartner->getId());
 			$newFlavorParams->save();
 			
 			KalturaLog::log("Copied [".$flavorParams->getId()."], new id is [".$newFlavorParams->getId()."]");
 		}
 	}
 	
 	public static function copyConversionProfiles(Partner $fromPartner, Partner $toPartner)
 	{
		$copiedList = array();
		
 		KalturaLog::log("Copying conversion profiles from partner [".$fromPartner->getId()."] to partner [".$toPartner->getId()."]");
 		
 		$c = new Criteria();
 		$c->add(conversionProfile2Peer::PARTNER_ID, $fromPartner->getId());
 		
 		$conversionProfiles = conversionProfile2Peer::doSelect($c);
 		foreach($conversionProfiles as $conversionProfile)
 		{
 			$newConversionProfile = $conversionProfile->copy();
 			$newConversionProfile->setPartnerId($toPartner->getId());
 			$newConversionProfile->save();
 			
 			KalturaLog::log("Copied [".$conversionProfile->getId()."], new id is [".$newConversionProfile->getId()."]");
			$copiedList[$conversionProfile->getId()] = $newConversionProfile->getId();
 			
 			$c = new Criteria();
 			$c->add(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, $conversionProfile->getId());
 			$fpcpList = flavorParamsConversionProfilePeer::doSelect($c);
 			foreach($fpcpList as $fpcp)
 			{
 				$flavorParamsId = $fpcp->getFlavorParamsId();
 				$flavorParams = assetParamsPeer::retrieveByPK($flavorParamsId);
 				if ($flavorParams && $flavorParams->getPartnerId() === 0) // copy the relation only if the flavor params are from partner 0 
 				{
	 				$newFpcp = $fpcp->copy();
	 				$newFpcp->setConversionProfileId($newConversionProfile->getId());
	 				$newFpcp->save();
 				}
 			}
 		}
 		
 		$toPartner->save();
		// make sure conversion profile is set on the new partner in case it was missed/skiped in the conversionProfile2::copy method
		if(!$toPartner->getDefaultConversionProfileId())
		{
			$fromPartnerDefaultProfile = $fromPartner->getDefaultConversionProfileId();
			if($fromPartnerDefaultProfile && key_exists($fromPartnerDefaultProfile, $copiedList))
			{
				$toPartner->setDefaultConversionProfileId($copiedList[$fromPartnerDefaultProfile]);
				$toPartner->save();
			}
		}
 	}
 	
 	public static function copyAccessControls(Partner $fromPartner, Partner $toPartner)
 	{
		$copiedList = array();
 		KalturaLog::log("Copying access control profiles from partner [".$fromPartner->getId()."] to partner [".$toPartner->getId()."]");
 		
 		$c = new Criteria();
 		$c->add(accessControlPeer::PARTNER_ID, $fromPartner->getId());
 		
 		$accessControls = accessControlPeer::doSelect($c);
 		foreach($accessControls as $accessControl)
 		{
 			$newAccessControl = $accessControl->copy();
 			$newAccessControl->setPartnerId($toPartner->getId());
 			$newAccessControl->save();
 			
 			KalturaLog::log("Copied [".$accessControl->getId()."], new id is [".$newAccessControl->getId()."]");
			$copiedList[$accessControl->getId()] = $newAccessControl->getId();
 		}

 		$toPartner->save();
		// make sure access control profile is set on the new partner
		if(!$toPartner->getDefaultAccessControlId())
		{
			$fromPartnerAccessControl = $fromPartner->getDefaultAccessControlId();
			if($fromPartnerAccessControl && key_exists($fromPartnerAccessControl, $copiedList))
			{
				$toPartner->setDefaultAccessControlId($copiedList[$fromPartnerAccessControl]);
				$toPartner->save();
			}
		}		
 	}
 	
 	/*
 	 * check partner status before delivering actual media files
 	 * checks also the requesting ip to be from non-blocked ips
 	 */
 	public static function blockInactivePartner($partnerId)
 	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (is_null($partner))
		{
			KalturaLog::log ( "BLOCK_PARNTER partner [$partnerId] doesnt exist" );
			KExternalErrors::dieError(KExternalErrors::PARTNER_NOT_FOUND);
		}
			
		$status = $partner->getStatus();
		if ($status != Partner::PARTNER_STATUS_ACTIVE)
		{
			KalturaLog::log ( "BLOCK_PARNTER_STATUS partner [$partnerId] status [$status]" );
			KExternalErrors::dieError(KExternalErrors::PARTNER_NOT_ACTIVE);
		}

		// take blocked-countries country code from partner custom data
		$blockContries = $partner->getDelivryBlockCountries();
		// if not set on partner custom data - take from kConf
		if(empty($blockContries) || is_null($blockContries))
		{
			// don't auto block paying partners
			if ($partner->getPartnerPackage() > PartnerPackages::PARTNER_PACKAGE_FREE)
			{
					return;
			}
			
			$blockContries = kConf::get ("delivery_block_countries" );
		}
		if ($blockContries)
		{
			// check if request is coming from the blocked country - and block if it is
			$currentCountry = null;
			$blockedCountry = requestUtils::matchIpCountry( $blockContries , $currentCountry );
			if ($blockedCountry)
			{
				KalturaLog::log ( "IP_BLOCK partner [$partnerId] from country [$currentCountry]" );
				KExternalErrors::dieError(KExternalErrors::IP_COUNTRY_BLOCKED);			
			}
		}
	}
	
	/*
	 * Ensure the request for media arrived in a way approved by the partner.
	 * this may include restricting to a specific cdn, enforcing token usage etc..
	 * Die in case of a breach.
	 * 
	 * @param int $partnerId
	 */
	public static function enforceDelivery($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK( $partnerId );
		if ( !$partner || (! $partner->getDeliveryRestrictions() ) )
			return;

		$deliveryRestrictions = $partner->getDeliveryRestrictions();
		$deliveryRestrictionsArr = explode(",", $deliveryRestrictions);
		
		$delivery = kUrlManager::getUrlManagerIdentifyRequest();
		
		$restricted = true;
		foreach($deliveryRestrictionsArr as $deliveryRestriction)
		{
			if ($deliveryRestriction === $delivery)
			{
				$restricted = false;
				break;
			}
		}
		
		if ($restricted)
		{
			KalturaLog::log ( "DELIVERY_METHOD_NOT_ALLOWED partner [$partnerId]" );
			KExternalErrors::dieError(KExternalErrors::DELIVERY_METHOD_NOT_ALLOWED);			
		}
	}
	
	public static function getPartnersArray(array $partnerIds, Criteria $c = null)
	{
		$ret = array();
		if (!$c)
		    $c = new Criteria();
		$c->addAnd(PartnerPeer::ID, $partnerIds, Criteria::IN);
		$c->addAnd(PartnerPeer::STATUS, Partner::PARTNER_STATUS_ACTIVE, Criteria::EQUAL);
		PartnerPeer::setUseCriteriaFilter(false);
		$partners = PartnerPeer::doSelect($c);
		PartnerPeer::setUseCriteriaFilter(true);
		foreach ($partners as $partner)
		{
			if (!in_array($partner->getId(), array(PartnerPeer::GLOBAL_PARTNER, Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID))) {
				$ret[] = $partner;
			}
		}
		return $ret;
	}
}
