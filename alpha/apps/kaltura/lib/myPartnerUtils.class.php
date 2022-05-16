<?php
class myPartnerUtils
{
	const PARTNER_SET_POLICY_NONE = 1;
	const PARTNER_SET_POLICY_IF_NULL = 2;
	const PARTNER_SET_POLICY_FORCE = 3;

	const PUBLIC_PARTNER_INDEX = 99;

	const PARTNER_GROUP = "__GROUP_PARTNER__";
	
	const ALL_PARTNERS_WILD_CHAR = "*";
	
	const BLOCKING_DAYS_GRACE = 7;

	const MARKETO_NEW_TRIAL_ACCOUNT = 'marketo_new_register_success_campaign';
	const MARKETO_NEW_ADDITIONAL_TRIAL_ACCOUNT = 'marketo_additional_register_success_campaign';
	const MARKETO_NEW_INTERNAL_TRIAL_ACCOUNT = 'marketo_new_register_internal_success_campaign';
	const MARKETO_MISSING_PASSWORD = 'marketo_missing_Password_campaign';
	const MARKETO_WRONG_PASSWORD = 'marketo_wrong_password_campaign';

	const TYPE_DOWNLOAD = 'download';
	
	private static $s_current_partner_id = null;
	private static $s_set_partner_id_policy  = self::PARTNER_SET_POLICY_NONE;

	private static $s_filterred_peer_list = array();
	private static $partnerCriteriaParams = array();
	//contains all partnerCriteriaParams, including params that were already retrieved
	private static $allPartnerCriteriaParams = array();

	public static function getAllPartnerCriteriaParams()
	{
		return self::$allPartnerCriteriaParams;
	}

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
		if( !in_array($partner->getStatus(), array(Partner::PARTNER_STATUS_ACTIVE,Partner::PARTNER_STATUS_READ_ONLY)))
		{
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
		unset(self::$allPartnerCriteriaParams[$objectName]);
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
		self::$allPartnerCriteriaParams = array();
	}

	// will reset all the filters used in the applyPartnerFilters and will re-apply them
	public static function reApplyPartnerFilters($allPartnerCriteriaParams)
	{
		if (!$allPartnerCriteriaParams)
		{
			KalturaLog::debug("could not re-apply filters, empty partnerCriteriaParams array was sent");
			return;
		}

		self::resetAllFilters();
		foreach($allPartnerCriteriaParams as $objectName => $partnerCriteriaParams)
		{
			list($partner_id, $private_partner_data, $partner_group, $kaltura_network) = $partnerCriteriaParams;
			self::addPartnerToCriteria($objectName, $partner_id, $private_partner_data, $partner_group, $kaltura_network);
		}
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
		self::$allPartnerCriteriaParams[$objectName] = self::$partnerCriteriaParams[$objectName];
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
		
		if ( !$partner ) 
			return array ( false , null );
		if( !$partner->getNotificationUrl() ) 
			return array ( false , null );
		
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
	
	
	public static function getCdnHost ( $partner_id, $protocol = null, $hostType = null )
	{
		$protocol = infraRequestUtils::getProtocol();

		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ($partner)
		{
			$whiteListHost = self::getWhiteListHost($partner);
			if (!is_null($whiteListHost))
			{
				$cdnHost = $protocol.'://'.$whiteListHost;
				if (isset($_SERVER['SERVER_PORT']))
				{
					$cdnHost .= ":".$_SERVER['SERVER_PORT'];
				}
				return $cdnHost;
			}
		}

		switch ($hostType)
		{
			case 'thumbnail':
				if ($partner && $partner->getThumbnailHost())
				{
					return preg_replace('/^https?/', $protocol, $partner->getThumbnailHost());
				}
				if ($partner && $partner->getCdnHost())
				{
					return preg_replace('/^https?/', $protocol, $partner->getCdnHost());
				}
				return requestUtils::getThumbnailCdnHost($protocol);
			case 'api':
				if ($protocol == 'https')
				{
					$apiHost = (kConf::hasParam('cdn_api_host_https')) ? kConf::get('cdn_api_host_https') : kConf::get('www_host');
					return 'https://' . $apiHost;
				}
				else
				{
					$apiHost = (kConf::hasParam('cdn_api_host')) ? kConf::get('cdn_api_host') : kConf::get('www_host');
					return 'http://' . $apiHost;
				}
				break;
			default:
				if ($partner && $partner->getCdnHost())
				{
					return preg_replace('/^https?/', $protocol, $partner->getCdnHost());
				}
				return requestUtils::getCdnHost($protocol);
		}
	}
	
	
	public static function getPlayServerHost($partner_id, $protocol = null)
	{
		if(is_null($protocol))
			$protocol = infraRequestUtils::getProtocol();

		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if (!$partner || !$partner->getPlayServerHost()) 
			return requestUtils::getPlayServerHost($protocol);

		$playServerHost = $partner->getPlayServerHost();
		$playServerHost = preg_replace('/^https?/', $protocol, $playServerHost);
			
		return $playServerHost;
	}
	
	public static function getThumbnailHost ($partner_id, $protocol = null)
	{
	    $partner = PartnerPeer::retrieveByPK( $partner_id );
	    if ( !$partner || (! $partner->getThumbnailHost() ) ) return self::getCdnHost($partner_id, $protocol, "thumbnail");
	    
	    // in case the request came through https, force https url
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$protocol = 'https';

		$thumbHost = $partner->getThumbnailHost();

		// temporary default is http since the system is not aligned to use https in all of its components (e.g. kmc)
		// right now, if a partner cdnHost is set to https:// the kmc wont work well if we reply with https prefix to its requests
		if ($protocol === null)
			$protocol='http';

		// if a protocol was set manually (or by the temporary http default above) use it instead of the partner setting
		$thumbHost = preg_replace('/^https?/', $protocol, $thumbHost);
			
		return $thumbHost;
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
	 * Check if the entry has any flavor related to it
	 * <br>NOTE: V1 conversion-profile / flavor-params are not supported
	 * @param entry $entry_id
	 * @return bool|null true = the entry has flavors; false the entry does not have flavors;
	 *                   null = Undetermined (e.g. V1 conversion profile / flavor params)
	 */
	public static function entryConversionProfileHasFlavors( $entry_id  )
	{
		$result = null;

		$entry = entryPeer::retrieveByPK( $entry_id );
		if ( $entry )
		{
			// Try to get the entrt's conversion profile id
			$conversionProfileId = $entry->getConversionProfileId();

			// Doesn't exist? ==> Try to get conversion quality
			if ( is_null( $conversionProfileId ) )
			{
				// conversion quality is an alias for conersion_profile_type ('low' , 'med' , 'hi' , 'hd' ... )
				$conversionProfileId = $entry->getConversionQuality();
			}

			// Doesn't exist? ==> Try to get default (partner level) conversion profile id
			if ( is_null( $conversionProfileId ) )
			{
				$partner = $entry->getPartner();
				if ( $partner )
				{
					// search for the default one on the partner
					$conversionProfileId = $partner->getDefaultConversionProfileId();
				}
			}

			// Reach a conslusion
			if ( ! is_null( $conversionProfileId ) )
			{
				$conversionProfile = conversionProfile2Peer::retrieveByPk( $conversionProfileId );

				if ( $conversionProfile )
				{
					$flavorParams = $conversionProfile->getflavorParamsConversionProfiles();

					if ( ! empty( $flavorParams ) )
					{
						$result = true;
					}
					else
					{
						$result = false;
					}
				}
			}
		}

		return $result;		
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
		
		$encrypted_data = KCryptoWrapper::encrypt_3des($partner_id, $key);	
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
	
	const KALTURA_MONTHLY_PACKAGE_EIGHTY_PERCENT_WARNING = 95;
 	const KALTURA_MONTHLY_PACKAGE_LIMIT_WARNING_1 = 96;
 	const KALTURA_MONTHLY_PACKAGE_LIMIT_WARNING_2 = 97;
	
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
		
		list($header, $data) = kKavaReportsMgr::getTable( $partner->getId(), myReportsMgr::REPORT_TYPE_PARTNER_USAGE_DASHBOARD ,
		 $reportFilter, 10000 , 1 , "", null);

		$avg_continuous_aggr_storage_mb_key = array_search('avg_continuous_aggr_storage_mb', $header);
		$sum_partner_bandwidth_kb_key = array_search('sum_partner_bandwidth_kb', $header);
		
        $relevant_row = count($data)-1;
          
		$totalStorage = $data[$relevant_row][$avg_continuous_aggr_storage_mb_key]; // MB
        $totalTraffic = $data[$relevant_row][$sum_partner_bandwidth_kb_key]; // KB
        $totalUsage = ($totalStorage*1024) + $totalTraffic; // (MB*1024 => KB) + KB

        return array( $totalStorage , $totalUsage , $totalTraffic );
    }

	private static function collectPartnerMonthlyStatisticsFromDWH($partner, $report_date)
    {
        $totalTranscoding = 0;
        $totalBandwith = 0;
        $totalStorage = 0;
        
        $fromDate = dateUtils::firstDayOfMonth($report_date);

		$reportFilter = new reportsInputFilter();
		$reportFilter->from_day = str_replace('-','',$fromDate);
		$reportFilter->to_day = str_replace('-','',$report_date);		
		list($header, $data) = kKavaReportsMgr::getTotal($partner->getId(), myReportsMgr::REPORT_TYPE_PARTNER_USAGE, $reportFilter, $partner->getId());

		$bandwidth_consumption = array_search('bandwidth_consumption', $header);
		$deleted_storage = array_search('deleted_storage', $header);
		$added_storage = array_search('added_storage', $header);
		$transcoding_consumption = array_search('transcoding_consumption', $header);	
		$totalBandwith = $data[$bandwidth_consumption]*1024; //KB
		$totalTranscoding = $data[$transcoding_consumption]*1024; //KB
		$totalStorage = $data[$added_storage]*1024 - $data[$deleted_storage]*1024; //KB
        return array( $totalStorage , $totalBandwith , $totalTranscoding );
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

		$reportFilter->extra_map[self::IS_FREE_PACKAGE_PLACE_HOLDER] = "FALSE";
		if ($partnerPackage['id'] == 1) // free package
			$reportFilter->extra_map[self::IS_FREE_PACKAGE_PLACE_HOLDER] = "TRUE";
		
		list($header, $data) = kKavaReportsMgr::getTable( $partner->getId(), myReportsMgr::REPORT_TYPE_PARTNER_BANDWIDTH_USAGE ,
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
			}
			elseif (myPartnerUtils::isPartnerCreatedAsMonitoredFreeTrial($partner))
			{
				KalturaLog::debug("Partner [" . $partner->getId() . "] trial account extended - monitored trial");
				return;
			}
			else{
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
		
		$block_notification_grace = time() - (dateUtils::DAY * self::BLOCKING_DAYS_GRACE);
		$delete_grace = time() -  (dateUtils::DAY * 30);
		
		$packages = new PartnerPackages();
		$partnerPackage = $packages->getPackageDetails($partner->getPartnerPackage());
		$divisionFactor = $partnerPackage['cycle_bw'];

		$monitoredFreeTrial = false;
		if(myPartnerUtils::isPartnerCreatedAsMonitoredFreeTrial($partner))
		{
			$monitoredFreeTrial = true;
			if ($partner->getPartnerPackage() == PartnerPackages::PARTNER_PACKAGE_DEVELOPER_TRIAL)
				$divisionFactor = $partnerPackage['cycle_bw_for_monitored_trial'];
		}
		$divisionFactor = ($divisionFactor !=0 ? $divisionFactor : 1);


		$report_date = date('Y-m').'-01';
        // We are now working with the DWH and a stored-procedure, and not with record type 6 on partner_activity.
        $report_date = dateUtils::todayOffset(-1);

		list ( $totalStorage , $totalUsage , $totalTraffic ) = myPartnerUtils::collectPartnerStatisticsFromDWH($partner, $partnerPackage, $report_date);
		$totalUsageGB = $totalUsage/1024/1024; // from KB to GB
		$percent = round( ($totalUsageGB / $divisionFactor)*100, 2);
		$partner->setPartnerUsagePercent($percent);

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
			if(!$monitoredFreeTrial)
			{
				$body_params = array($partner->getAdminName(), $partnerPackage['cycle_bw'], $mindtouch_notice, round($totalUsageGB, 2), $email_link_hash);
				myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_PACKAGE_EIGHTY_PERCENT_WARNING, $partner, $body_params);
			}
		}
		elseif ($percent >= 80 &&
			$percent < 100 &&
			$partner->getEightyPercentWarning() &&
			!$partner->getUsageLimitWarning())
		{
			KalturaLog::log("passed the 80%, assume notification sent, nothing to do.");
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
			/* prepare mail job, and set getUsageLimitWarning() date */
			$partner->setUsageLimitWarning(time());
			// if ($partnerPackage['cycle_fee'] == 0) - script always works on free partners anyway
			if(!$monitoredFreeTrial)
			{
				KalturaLog::debug("partner ". $partner->getId() ." reached 100% - setting second warning");
				$body_params = array ( $partner->getAdminName(), $mindtouch_notice, round($totalUsageGB, 2), $email_link_hash );
				myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_PACKAGE_LIMIT_WARNING_1, $partner, $body_params);
			}
			else
			{
				KalturaLog::debug("partner ". $partner->getId() ." reached 100% - blocking partner");
				if($should_block_delete_partner)
				{
					$partner->setStatus(Partner::PARTNER_STATUS_CONTENT_BLOCK);
				}
			}
		}
		elseif ($percent >= 100 &&
				$partnerPackage['cycle_fee'] == 0 &&
				$partner->getUsageLimitWarning() > 0 && 
				$partner->getUsageLimitWarning() <= $block_notification_grace &&
				$partner->getUsageLimitWarning() > $delete_grace &&
				$partner->getStatus() != Partner::PARTNER_STATUS_CONTENT_BLOCK)
		{
			KalturaLog::debug("partner ". $partner->getId() ." reached 100% ".self::BLOCKING_DAYS_GRACE ." days ago - sending block email and blocking partner");
				
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
				$partner->getUsageLimitWarning() <= $block_notification_grace &&
				!$monitoredFreeTrial)
		{
			$body_params = array ( $partner->getAdminName(), round($totalUsageGB, 2) );
			myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_PAID_PACKAGE_SUGGEST_UPGRADE, $partner, $body_params);
		}
		elseif ($percent >= 100 &&
				$partnerPackage['cycle_fee'] == 0 &&
				$partner->getUsageLimitWarning() > 0 &&
				$partner->getUsageLimitWarning() <= $delete_grace &&
				$partner->getStatus() == Partner::PARTNER_STATUS_CONTENT_BLOCK &&
				!$monitoredFreeTrial)
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

	public static function doMonthlyPartnerUsage(Partner $partner)
	{
		KalturaLog::debug("Validating partner [" . $partner->getId() . "]");
		
		$packages = new PartnerPackages();
		$partnerPackage = $packages->getPackageDetails($partner->getPartnerPackage());
		if($partnerPackage[PartnerPackages::PACKAGE_CYCLE_FEE] != 0){
			KalturaLog::debug("Partner has paid package, skipping validation [" . $partner->getId() . "]");
			return;
		}
				
		$block_notification_grace = time() - (dateUtils::DAY * self::BLOCKING_DAYS_GRACE);
		$delete_grace = time() -  (dateUtils::DAY * 30);
		
		$report_date = dateUtils::todayOffset(-1);

		list ( $monthlyStorage, $monthlyTraffic, $monthlyTranscoding ) = myPartnerUtils::collectPartnerMonthlyStatisticsFromDWH($partner, $report_date);

		$email_link_hash = 'pid='.$partner->getId().'&h='.(self::getEmailLinkHash($partner->getId(), $partner->getSecret()));
		
		self::validatePartnerMonthlyUsagePerType($partner, $partnerPackage, $monthlyStorage, PartnerPackages::PACKAGE_STORAGE_USAGE, $report_date, $block_notification_grace, $delete_grace, $email_link_hash);
		self::validatePartnerMonthlyUsagePerType($partner, $partnerPackage, $monthlyTraffic, PartnerPackages::PACKAGE_TRAFFIC_USAGE, $report_date, $block_notification_grace, $delete_grace, $email_link_hash);
		self::validatePartnerMonthlyUsagePerType($partner, $partnerPackage, $monthlyTranscoding, PartnerPackages::PACKAGE_TRANSCODING_USAGE, $report_date, $block_notification_grace, $delete_grace, $email_link_hash);
		
		$partner->save();		
	}
	
	private static function validatePartnerMonthlyUsagePerType($partner, $partnerPackage, $usage, $usageType, $report_date, $block_notification_grace, $delete_grace, $email_link_hash)
	{
		if(!array_key_exists($usageType, $partnerPackage)){
			return;
		}
		
		$usageGB = $usage/1024/1024; // from KB to GB
		$percent = round( ($usageGB / $partnerPackage[$usageType])*100, 2);
		$notificationId = 0;
		
		KalturaLog::debug("percent (".$partner->getId().") is: $percent for usage type $usageType");
				
		//check if partner should be deleted
		if($partner->getStatus() == Partner::PARTNER_STATUS_CONTENT_BLOCK )
		{
			$warning_100 = $partner->getUsageWarning($usageType, 100);
			if($warning_100 > 0 && $warning_100 <= $delete_grace)
			{
				KalturaLog::debug("partner ". $partner->getId() ." reached 100% a month ago - deleting partner");
					
				/* delete partner */
				$notificationId = myPartnerUtils::KALTURA_DELETE_ACCOUNT;
				$partner->setStatus(Partner::PARTNER_STATUS_DELETED);				
			}
		}
		else
		{
			self::resetMonthlyUsageWarningIfNotRelevant($partner, $usageType, $percent, $report_date);
		
			$warning_80 = $partner->getUsageWarning($usageType, 80);
			$warning_100 = $partner->getUsageWarning($usageType, 100);
			
			if($percent >= 80 && $percent < 100 && !$warning_80) //send 80% usage warning
			{
				KalturaLog::debug("partner ". $partner->getId() ." reached 80% - setting first warning for usage ". $usageType);
				$partner->setUsageWarning($usageType, 80, time());
				$partner->resetUsageWarning($usageType, 100);
				$notificationId = myPartnerUtils::KALTURA_MONTHLY_PACKAGE_EIGHTY_PERCENT_WARNING;				
			}
			elseif ($percent >= 80 && $percent < 100 && $warning_80 && !$warning_100)
			{
				KalturaLog::log("passed the 80%, assume notification sent, nothing to do.");
			}
			elseif ($percent >= 100 && !$warning_100) // send 100% usage warning
			{
				KalturaLog::debug("partner ". $partner->getId() ." reached 100% - setting second warning for usage ". $usageType);
				$partner->setUsageWarning($usageType, 100, time());
				$notificationId = myPartnerUtils::KALTURA_MONTHLY_PACKAGE_LIMIT_WARNING_1;
			}
			elseif ($percent >= 100 && $warning_100 > 0 && $warning_100 <= $block_notification_grace && $warning_100 > $delete_grace)
			{
				KalturaLog::debug("partner ". $partner->getId() ." reached 100% ". self::BLOCKING_DAYS_GRACE ." days ago - sending block email and blocking partner");				
				/* send block email and block partner */
				$notificationId = myPartnerUtils::KALTURA_MONTHLY_PACKAGE_LIMIT_WARNING_2;			
				$partner->setStatus(Partner::PARTNER_STATUS_CONTENT_BLOCK);
			}
		}
		if($notificationId)
		{
			$body_params = array();
			$usageText = PartnerPackages::getPackageUsageText($usageType);
			switch($notificationId){
				case myPartnerUtils::KALTURA_MONTHLY_PACKAGE_EIGHTY_PERCENT_WARNING:
					$body_params = array ( $partner->getAdminName(), $partnerPackage[$usageType], $usageText, round($usageGB, 2), $email_link_hash );
					break;
				case myPartnerUtils::KALTURA_MONTHLY_PACKAGE_LIMIT_WARNING_1:
					$body_params = array ( $partner->getAdminName(), $partnerPackage[$usageType], $usageText, round($usageGB, 2), $email_link_hash );
					break;
				case myPartnerUtils::KALTURA_MONTHLY_PACKAGE_LIMIT_WARNING_2:
					$body_params = array ( $partner->getAdminName(), $partnerPackage[$usageType], $usageText, round($usageGB, 2), $email_link_hash );
					break;
				case myPartnerUtils::KALTURA_DELETE_ACCOUNT:
					$body_params = array ( $partner->getAdminName() );
					break;
			}
			myPartnerUtils::notifiyPartner($notificationId, $partner, $body_params);
		}					
	}
	
	private static function resetMonthlyUsageWarningIfNotRelevant($partner, $usageType, $percent, $report_date)
	{
		$warning_80 = $partner->getUsageWarning($usageType, 80);
		$warning_100 = $partner->getUsageWarning($usageType, 100);
		if($warning_100 || $warning_80)
		{
			$isCurrent = true;
			$warningMonth = $warning_80 ? date("m", $warning_80) : date("m", $warning_100);
			$dateObj = new DateTime($report_date);
			$reportMonth = date("m", $dateObj->getTimestamp());
			$isCurrent = ($warningMonth == $reportMonth);
		
			if($percent < 80 || !$isCurrent )
			{
				KalturaLog::debug("Reseting partner ". $partner->getId() ." warnings for usage ". $usageType);		
				$partner->resetUsageWarning($usageType, 80);
				$partner->resetUsageWarning($usageType, 100);			
			}			
		}
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
		
		$data = kKavaReportsMgr::getGraph($partner->getId(), $reportType, $reportFilter, null, null);
		
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
		//$startDate must be passed as Unix time stamp value
		$daysInMonth = date('t', $startDate);
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
 	
		self::saveTemplateObjectsNum($fromPartner, $toPartner);
 		// Launch a batch job that will copy the heavy load as an async operation 
  		kJobsManager::addCopyPartnerJob( $fromPartner->getId(), $toPartner->getId() );
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
			$UserRolesNamesToIgnore = kConf::get('partner_copy_user_roles_ignore_list', 'local', array());
 			if (in_array($role->getName(), $UserRolesNamesToIgnore))
			{
				continue;
			}
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
 	
 	public static function copyConversionProfiles(Partner $fromPartner, Partner $toPartner, $permissionRequiredOnly = false)
 	{
		$copiedList = array();
		
 		KalturaLog::log("Copying conversion profiles from partner [".$fromPartner->getId()."] to partner [".$toPartner->getId()."]");
 		
 		$c = new Criteria();
 		$c->add(conversionProfile2Peer::PARTNER_ID, $fromPartner->getId());
 		
 		$conversionProfiles = conversionProfile2Peer::doSelect($c);
 		foreach($conversionProfiles as $conversionProfile)
 		{
 			/* @var $conversionProfile conversionProfile2 */
 			if ($permissionRequiredOnly && !count($conversionProfile->getRequiredCopyTemplatePermissions()))
 				continue;
 			
 			if (!self::isPartnerPermittedForCopy ($toPartner, $conversionProfile->getRequiredCopyTemplatePermissions()))
 				continue;
 				
 			$newConversionProfile = $conversionProfile->copy();
 			$newConversionProfile->setPartnerId($toPartner->getId());
 			try {
 				$newConversionProfile->save();
 			}
 			catch (Exception $e)
 			{
 				KalturaLog::info("Exception occured, conversion profile was not copied. Message: [" . $e->getMessage() . "]");
 				continue;
 			}
 			
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
 		
		// make sure conversion profile is set on the new partner in case it was missed/skiped in the conversionProfile2::copy method
		if(!$toPartner->getDefaultConversionProfileId())
		{
			$fromPartnerDefaultProfile = $fromPartner->getDefaultConversionProfileId();
			if($fromPartnerDefaultProfile && key_exists($fromPartnerDefaultProfile, $copiedList))
			{
				$toPartner->setDefaultConversionProfileId($copiedList[$fromPartnerDefaultProfile]);
			}
		}
 	
		if(!$toPartner->getDefaultLiveConversionProfileId())
		{
			$fromPartnerDefaultLiveProfile = $fromPartner->getDefaultLiveConversionProfileId();
			if($fromPartnerDefaultLiveProfile && key_exists($fromPartnerDefaultLiveProfile, $copiedList))
			{
				$toPartner->setDefaultLiveConversionProfileId($copiedList[$fromPartnerDefaultLiveProfile]);
			}
		}
		
 		$toPartner->save();
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
		$blockCountries = $partner->getDelivryBlockCountries();
		// if not set on partner custom data - take from kConf
		if(empty($blockCountries) || is_null($blockCountries))
		{
			// don't auto block paying partners
			if ($partner->getPartnerPackage() > PartnerPackages::PARTNER_PACKAGE_FREE)
			{
					return;
			}
			
			$blockCountries = kConf::get ("delivery_block_countries" );
		}
		if ($blockCountries)
		{
			// check if request is coming from the blocked country - and block if it is
			if (!myPartnerUtils::isRequestFromAllowedCountry($blockCountries, $partnerId))
			{
				KExternalErrors::dieError(KExternalErrors::IP_COUNTRY_BLOCKED);
			}
		}
	}
	
	public static function isRequestFromAllowedCountry($blockedCountriesList, $partnerId)
	{
		$currentCountry = null;
		$blockedCountry = requestUtils::matchIpCountry($blockedCountriesList, $currentCountry);
		if ($blockedCountry)
		{
			KalturaLog::err("IP_BLOCK partner [$partnerId] from country [$currentCountry]");
			return false;
		}
		
		return true;
	}

	/**
	 * Ensure the request for media arrived in a way approved by the partner.
	 * this may include restricting to a specific cdn, enforcing token usage etc..
	 * Die in case of a breach.
	 *
	 * @param entry $entry
	 * @param asset $asset
	 * @param $storageProfileId
	 */
	public static function enforceDelivery($entry, $asset = null, $storageProfileId = null)
	{
		// block inactive partner
		$partnerId = $entry->getPartnerId();
		self::blockInactivePartner($partnerId);

		// validate serve access control
		$flavorParamsId = $asset ? $asset->getFlavorParamsId() : null;
		$secureEntryHelper = new KSecureEntryHelper($entry, null, null, ContextType::SERVE, array(), $asset);
		$validServe = $secureEntryHelper->validateForServe($flavorParamsId);

		if(!is_null($storageProfileId))
		{
			$downloadAllowed = self::isDownloadAllowed($storageProfileId, $entry->getId());
			switch($downloadAllowed)
			{
				case kUrlRecognizer::RECOGNIZED_OK:
					return;
				case kUrlRecognizer::RECOGNIZED_NOT_OK:
					KalturaLog::debug('Failed to recognize url due to wrong or missing signing');
					KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'Failed to parse signature');
					break;
			}
		}

		if(!$validServe)
		{
			KExternalErrors::dieError(KExternalErrors::ACCESS_CONTROL_RESTRICTED);
		}

		// enforce delivery
		$partner = PartnerPeer::retrieveByPK($partnerId);		// Note: Partner was already loaded by blockInactivePartner, no need to check for null
		
		$restricted = DeliveryProfilePeer::isRequestRestricted($partner);
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
	
	/**
	 * 
	 * @param Partner $toPartner
	 * @param array $permissionArray
	 * 
	 * @return bool
	 */
	public static function isPartnerPermittedForCopy (Partner $toPartner, array $permissionArray)
	{
		foreach ($permissionArray as $permission)
		{
			if (!PermissionPeer::isValidForPartner($permission, $toPartner->getId()))
			{
				return false;
			}
			
		}	
		return true;
	}

	/**
	 * @param Partner $partner
	 * @return null
	 */
	public static function getWhiteListHost(Partner $partner)
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
		{
			$xForwardedHosts = explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
			foreach($xForwardedHosts as $xForwardedHost){
				if ($partner->isInCDNWhiteList($xForwardedHost))
				{
					return $xForwardedHost;
				}
			}
		}
		elseif (isset($_SERVER['HTTP_HOST']) && $partner->isInCDNWhiteList($_SERVER['HTTP_HOST']))
		{
			return $_SERVER['HTTP_HOST'];
		}
		return null;
	}

	/**
	 * The function checks for new free trial partners if its time to block/delete them and whether
	 * we need to sync their lead in Marketo
	 *
	 * @param partner $partner
	 */
	public static function handleDayInFreeTrial(Partner $partner)
	{
		$packages = new PartnerPackages();
		$partnerPackageInfo = $packages->getPackageDetails($partner->getPartnerPackage());

		$endDay = $partnerPackageInfo['trial_num_days'];
		$deletionDay = $partnerPackageInfo['trial_num_days_until_deletion'];

		if($partner->getExtendedFreeTrailExpiryDate())
		{
			$formattedExtensionDate = date('Y-m-d H:i:s', $partner->getExtendedFreeTrailExpiryDate());
			$endDay = dateUtils::diffInDays($partner->getCreatedAt(), $formattedExtensionDate);
			$deletionDay = $endDay + 30;
			KalturaLog::debug("After trial extension the End day is: [$endDay]");
		}

		$freeTrialUpdatesDays = explode(',', $partnerPackageInfo['notification_days']);

		$dayInFreeTrial = dateUtils::diffInDays($partner->getCreatedAt(), dateUtils::today());
		KalturaLog::debug("partner [{$partner->getId()}] is currently at the [$dayInFreeTrial] day of free trial");

		// in case we want to delete/block partner that reached to specific day we wil disable this line
		//$partner = self::checkIfPartnerStatusChangeRequired($partner, $dayInFreeTrial, $endDay, $deletionDay);
		if($freeTrialUpdatesDays)
			$partner = self::checkForNotificationDay($partner, $dayInFreeTrial, $freeTrialUpdatesDays);

		$partner->save();
	}


	public static function checkIfPartnerStatusChangeRequired($partner, $dayInFreeTrial, $endDay, $deletionDay)
	{
		if (($dayInFreeTrial >= $endDay) && ($dayInFreeTrial < $deletionDay))
		{
			KalturaLog::debug('Partner ['.$partner->getId().'] reached to end of free trial day. Blocking content.');
			$partner->setStatus(Partner::PARTNER_STATUS_CONTENT_BLOCK);
		}

		if ($dayInFreeTrial >= $deletionDay)
		{
			KalturaLog::debug('Partner ['.$partner->getId().'] reached to free trial deletion day. Deleting partner.');
			$partner->setStatus(Partner::PARTNER_STATUS_DELETED);
		}
		return $partner;
	}

	public static function checkForNotificationDay($partner, $dayInFreeTrial, $freeTrialUpdatesDays)
	{
		$closestUpdatesDay = self::getClosestDay($dayInFreeTrial, $freeTrialUpdatesDays);
		KalturaLog::debug('closest notification day comparing today [' . $closestUpdatesDay . ']');
		if ($closestUpdatesDay > $partner->getLastFreeTrialNotificationDay())
		{
			KalturaLog::debug('Partner [' . $partner->getId() . '] reached to one of the Marketo lead sync days.');
			$partner->setLastFreeTrialNotificationDay($dayInFreeTrial);
		}
		return $partner;
	}

	/**
	 * retrieve the closest (lowest) notification day compering today
	 *
	 * @param int $search
	 * @param array $arr
	 * @return int
	 */
	public static function getClosestDay($search, $arr) {
		$closest = 0;
		foreach ($arr as $item)
		{
			if (($item <= $search) && (abs($search - $closest) > abs($item - $search)))
				$closest = $item;
		}
		return $closest;
	}


	/**
	 * save in partner's custom data the number of objects from different type that were created
	 * as part of the partner population from template partner
	 *
	 * @param partner $fromPartner
	 * @param partner $toPartner
	 */
	public static function saveTemplateObjectsNum(Partner $fromPartner, Partner $toPartner)
	{
		KalturaLog::log('Saving the number of entries, categories and metadata objects exist on partner ['.$fromPartner->getId().'] on partner ['.$toPartner->getId().']');
		$fromPartnerId = $fromPartner->getId();

		$categoriesNum = self::getCategoriesNumForPartner($fromPartnerId);
		$toPartner->setTemplateCategoriesNum($categoriesNum);

		$entriesNum = self::getEntriesNumForPartner($fromPartnerId);
		$toPartner->setTemplateEntriesNum($entriesNum);

		$metadataNum = self::getMetadataObjectsNumForPartner($fromPartnerId);
		$toPartner->setTemplateCustomMetadataNum($metadataNum);

		$toPartner->save();
	}

	/**
	 * calculate the number of categories using partner and status
	 *
	 * @param string $partnerId
	 * @param bool $onlyActive
	 * @return int $categoriesNum
	 */
	public static function getCategoriesNumForPartner($partnerId, $onlyActive = true)
	{
		categoryPeer::setUseCriteriaFilter(false);
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$c->addAnd(categoryPeer::PARTNER_ID, $partnerId);
		if($onlyActive)
			$c->addAnd(categoryPeer::STATUS, CategoryStatus::ACTIVE);
		$c->applyFilters();
		$totalCount = $c->getRecordsCount();
		categoryPeer::setUseCriteriaFilter(true);
		return $totalCount;
	}

	/**
	 * calculate the number of entries using partner and status
	 *
	 * @param string $partnerId
	 * @param bool $onlyReady
	 * @return int $entriesNum
	 */
	public static function getEntriesNumForPartner($partnerId, $onlyReady = true)
	{
		entryPeer::setUseCriteriaFilter(false);
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$c->addAnd(entryPeer::PARTNER_ID, $partnerId);
		if($onlyReady)
			$c->addAnd(entryPeer::STATUS, entryStatus::READY);
		$c->applyFilters();
		$totalCount = $c->getRecordsCount();
		entryPeer::setUseCriteriaFilter(true);
		return $totalCount;
	}

	/**
	 * calculate the number of custome metadata objects using partner and status
	 *
	 * @param string $partnerId
	 * @return int $metadataNum
	 */
	public static function getMetadataObjectsNumForPartner($partnerId)
	{
		kCurrentContext::$partner_id = $partnerId;

		$entrySearch = new kEntrySearch();
		$categorySearch = new kCategorySearch();
		$userSearch = new kUserSearch();
		$objectStatuses = array();

		$totalCustomMetadadaCount = 0;
		$totalCustomMetadadaCount += self::getMetadataSearchParameters($entrySearch, $objectStatuses);
		kBaseElasticEntitlement::$isInitialized = false;
		$totalCustomMetadadaCount += self::getMetadataSearchParameters($categorySearch, $objectStatuses);
		kBaseElasticEntitlement::$isInitialized = false;
		$totalCustomMetadadaCount += self::getMetadataSearchParameters($userSearch, $objectStatuses);
		return $totalCustomMetadadaCount;
	}

	protected static function getMetadataSearchParameters($baseSearch, $objectStatuses)
	{
		$mdItem = new ESearchMetadataItem();
		$mdItem->setItemType(ESearchItemType::EXISTS);
		$searchItems = array($mdItem);
		$operator = new ESearchOperator();
		$operator->setOperator(ESearchOperatorType::AND_OP);
		$operator->setSearchItems($searchItems);
		$pager = new kPager();
		$pager->setPageSize(0);

		try
		{
			$results = $baseSearch->doSearch($operator, $pager, $objectStatuses, null, null);
		}
		catch(Exception $e)
		{
			return 0;
		}

		return $results[kESearchCoreAdapter::HITS_KEY][kESearchCoreAdapter::TOTAL_KEY];
	}

	/**
	 * calculate the number of categories that the partner created after the account population
	 * (exclude objects that were from template partner)
	 *
	 * @param partner $partner
	 * @return int $partnerCategories
	 */
	public static function getNumOfCategoriesCreatedByPartner ($partner)
	{
		$partnerId = $partner->getId();
		$categories = self::getCategoriesNumForPartner($partnerId, false);
		$partnerCategories = $categories - $partner->getTemplateCategoriesNum();
		return $partnerCategories;
	}

	/**
	 * calculate the number of entries that the partner created after the account population
	 * (exclude objects that were from template partner)
	 *
	 * @param partner $partner
	 * @return int $partnerEntries
	 */
	public static function getNumOfEntriesCreatedByPartner ($partner)
	{
		$partnerId = $partner->getId();
		$entries = self::getEntriesNumForPartner($partnerId, false);
		$partnerEntries = $entries - $partner->getTemplateEntriesNum();
		return $partnerEntries;
	}

	/**
	 * calculate the number of custom metadata objects that the partner created after the account population
	 * (exclude objects that were from template partner)
	 *
	 * @param partner $partner
	 * @return int $partnerMetadata
	 */
	public static function getNumOfMetadataObjectsCreatedByPartner ($partner)
	{
		$partnerId = $partner->getId();
		$MetadataObjects = self::getMetadataObjectsNumForPartner($partnerId);
		$partnerMetadata = $MetadataObjects - $partner->getTemplateCustomMetadataNum();
		return $partnerMetadata;
	}


	/**
	 * calculate the partner percent usage
	 *
	 * @param partner $partner
	 * @return int $percent
	 */
	public static function retrievePartnerUsagePercent($partner)
	{
		$packages = new PartnerPackages();
		$partnerPackage = $packages->getPackageDetails($partner->getPartnerPackage());
		$report_date = date('Y-m').'-01';
		list ( $totalStorage , $totalUsage , $totalTraffic ) = myPartnerUtils::collectPartnerStatisticsFromDWH($partner, $partnerPackage, $report_date);
		$totalUsageGB = $totalUsage/1024/1024; // from KB to GB
		$percent = round( ($totalUsageGB / $partnerPackage['cycle_bw'])*100, 2);
		return $percent;
	}

	/**
	 *  check if partner was created after we started the new free trial flow
	 *
	 * @param partner $partner
	 * @param bool useCurrentTime
	 * @return bool
	 */
	public static function isPartnerCreatedAsMonitoredFreeTrial($partner, $useCurrentTime = false)
	{
		if ($partner->getPartnerPackage() == PartnerPackages::PARTNER_PACKAGE_INTERNAL_TRIAL)
			return true;

		$freeTrialStartDate = myPartnerUtils::getFreeTrialStartDate($partner);
		if(!$freeTrialStartDate)
			return false;
		$createTime = $partner->getCreatedAt();
		if($useCurrentTime)
			$createTime = date('Y-m-d H:i:s');
		if($createTime >= $freeTrialStartDate)
			return true;
		return false;
	}


	public static function getFreeTrialStartDate($partner)
	{
		if ($partner->getPartnerPackage() == PartnerPackages::PARTNER_PACKAGE_DEVELOPER_TRIAL)
			$freeTrialStartDate = kConf::get('new_developer_free_trial_start_date','local', null);
		else
			$freeTrialStartDate = kConf::get('new_free_trial_start_date','local', null);
		return $freeTrialStartDate;
	}


	/**
	 *  retrieve all the partners in status active with specific admin email and package type
	 *
	 * @param partner $partner
	 * @param $package
	 * @return array
	 */
	public static function retrieveNotDeletedPartnerByEmailAndPackage ($partner, $package)
	{
		$c = new Criteria();
		$c->add(PartnerPeer::ADMIN_EMAIL, $partner->getAdminEmail());
		$c->add(PartnerPeer::PARTNER_PACKAGE, $package);
		$c->add(PartnerPeer::STATUS, KalturaPartnerStatus::DELETED, Criteria::NOT_EQUAL);
		$result = PartnerPeer::doSelectOne($c);
		return $result;
	}


	public static function initialPasswordSetForFreeTrial($loginData)
	{
		$partner = PartnerPeer::retrieveByPK($loginData->getConfigPartnerId());
		$freeTrialTypes = PartnerPackages::getFreeTrialPackages();
		if(in_array($partner->getPartnerPackage(), $freeTrialTypes) && myPartnerUtils::isPartnerCreatedAsMonitoredFreeTrial($partner))
		{
			$partner->setInitialPasswordSet(true);
			$partner->save();
		}
	}

	public static function getAuthenticationType($partner)
	{
		if($partner->getUseSso())
		{
			return PartnerAuthenticationType::SSO;
		}
		else if($partner->getUseTwoFactorAuthentication())
		{
			return PartnerAuthenticationType::TWO_FACTOR_AUTH;
		}
		return PartnerAuthenticationType::PASSWORD_ONLY;
	}

	public static function isDownloadAllowed ($storageProfileId, $entryId)
	{
		$downloadDeliveryProfile = self::getDownloadDeliveryProfile($storageProfileId, $entryId);
		if(!$downloadDeliveryProfile)
		{
			return kUrlRecognizer::NOT_RECOGNIZED;
		}

		$downloadRecognizer = $downloadDeliveryProfile->getRecognizer();
		if($downloadRecognizer)
		{
			return $downloadRecognizer->isRecognized(null);
		}

		return kUrlRecognizer::NOT_RECOGNIZED;
	}

	public static function getDownloadDeliveryProfile($storageProfileId, $entryId)
	{
		$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
		if(!$storageProfile)
		{
			return null;
		}

		$deliveryProfileIds = $storageProfile->getDeliveryProfileIds();
		if(!$deliveryProfileIds || !isset($deliveryProfileIds[self::TYPE_DOWNLOAD]))
		{
			return null;
		}
		$downloadDeliveryProfileId = $deliveryProfileIds[self::TYPE_DOWNLOAD][0];
		$downloadDeliveryProfile = DeliveryProfilePeer::retrieveByPK($downloadDeliveryProfileId);

		if($downloadDeliveryProfile)
		{
			$deliveryAttributes = DeliveryProfileDynamicAttributes::init(null, $entryId, PlaybackProtocol::HTTP);
			$downloadDeliveryProfile->setDynamicAttributes($deliveryAttributes);
		}

		return $downloadDeliveryProfile;
	}

}
