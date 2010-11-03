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
	 * will use cache to reduce the times the partner table is hit (hardley changes over time)
	 */
	public static function isValidSecret ( $partner_id , $partner_secret , $partner_key , &$ks_max_expiry_in_seconds , $admin = false  )
	{
		// TODO - handle errors
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner ) return Partner::VALIDATE_WRONG_LOGIN;

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



	// will reset all the filters used in the applyPartnerFilters
	public static function resetAllFilters()
	{
		foreach ( self::$s_filterred_peer_list as $peer )
		{
			$peer->setDefaultCriteriaFilter();
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

//echo __METHOD__ . ":" . "[$partner_id] , [$private_partner_data] , [$partner_group] , [$kaltura_network]" ;		
		// TODO - set !
		// make sure to pass the $partner_group &  $kaltura_network to objects where they are appropriate
/*
		self::addPartnerToCriteria ( kuserPeer::getCriteriaFilter() , kuserPeer::PARTNER_ID ,  $partner_id );
		self::addPartnerToCriteria ( entryPeer::getCriteriaFilter() , entryPeer::PARTNER_ID ,  $partner_id );
		self::addPartnerToCriteria ( kshowPeer::getCriteriaFilter() , kshowPeer::PARTNER_ID ,  $partner_id );
		self::addPartnerToCriteria ( moderationPeer::getCriteriaFilter() , moderationPeer::PARTNER_ID ,  $partner_id );
		self::addPartnerToCriteria ( notificationPeer::getCriteriaFilter() , notificationPeer::PARTNER_ID ,  $partner_id );
*/
		
		self::addPartnerToCriteria ( new kuserPeer() , $partner_id , $private_partner_data, $partner_group);
		self::addPartnerToCriteria ( new entryPeer() , $partner_id , $private_partner_data, $partner_group , $kaltura_network );
		self::addPartnerToCriteria ( new kshowPeer() , $partner_id , $private_partner_data, $partner_group , $kaltura_network );
		self::addPartnerToCriteria ( new moderationPeer() , $partner_id , $private_partner_data , $partner_group);
		self::addPartnerToCriteria ( new notificationPeer() , $partner_id , $private_partner_data , $partner_group);
		self::addPartnerToCriteria ( new categoryPeer() , $partner_id , $private_partner_data , $partner_group);
		
		// TODO - due to very bad performance every time there is such a call, make sure this code is called from the uiConf services
//		self::addPartnerToCriteria ( new uiConfPeer() , $partner_id );

		//		self::addPartnerToCriteria ( new widgetPeer(), $partner_id );
//		self::addPartnerToCriteria ( new PuserKuserPeer() , $partner_id );
//		self::addPartnerToCriteria ( new BatchJobPeer(), $partner_id );
	}

	private static function _addPartnerToCriteria ( $criteria_filter , $partner_field_name  , $partner_id )
	{
		$criteria = $criteria_filter->getFilter();
		$criteria->addAnd ( $partner_field_name , $partner_id );
		$criteria_filter->enable();
	}
	
	// if only partner_id exists - force it on the criteria
	// if also $partner_group - allow or partner_id or the partner_group - use in ( partner_id ,  $partner_group ) - where partner_group is split by ','
	// if partner_group == "*" - don't filter at all
	// if $kaltura_network - add 'or  display_in_search >= 2'
	public static function addPartnerToCriteria ( $peer , $partner_id , $private_partner_data = false , $partner_group=null , $kaltura_network=null )
	{
		self::$s_filterred_peer_list[] = $peer;
		
		$criteria_filter = $peer->getCriteriaFilter();
		$criteria = $criteria_filter->getFilter();
		
		$partner_field_name = $peer->translateFieldName( "partner_id" , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
		
		if( !$private_partner_data )
		{
			// the private partner data is not allowed - 
			if ( $kaltura_network )
			{
				// allow only the kaltura netword stuff
				$display_in_search_field_name = $peer->translateFieldName( "display_in_search" , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
				$criterion = $criteria->getNewCriterion( $display_in_search_field_name , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK , Criteria::GREATER_EQUAL );
//				$criterion->addAnd ( $criterion2 ) ;
				if ( $partner_id )
				{
					$order_by = "({$partner_field_name}<>{$partner_id})";  // first take the pattner_id and then the rest
					myCriteria::addComment( $criteria , "Only Kaltura Network" );
					$criteria->addAscendingOrderByColumn ( $order_by );//, Criteria::CUSTOM );
				}
			}
			else
			{
				// no private data and no kaltura_network - 
				// add a criteria that will return nothing
				$criterion = $criteria->getNewCriterion( $partner_field_name , Partner::PARTNER_THAT_DOWS_NOT_EXIST ) ;
			}
			
			$criteria->addAnd( $criterion );
		}
		else
		{
			// private data is allowed
			if ( empty ($partner_group ) && empty ( $kaltura_network ) )
			{
				// the default case
				$criteria->addAnd ( $partner_field_name , $partner_id );
			}
			elseif ( $partner_group == self::ALL_PARTNERS_WILD_CHAR )
			{
				// all is allowed - don't add anything to the criteria
			}
			else 
			{
				if ( $partner_group )
				{
					// $partner_group hold a list of partners separated by ',' or $kaltura_network is not empty (should be mySearchUtils::KALTURA_NETWORK = 'kn')
					$partners = explode ( "," , trim($partner_group ));
					foreach ( $partners as &$p ) { trim($p); } // make sure there are not leading or trailing spaces
	
					// add the partner_id to the partner_group
					$partners[] = $partner_id;
					
					$criterion = $criteria->getNewCriterion( $partner_field_name , $partners , Criteria::IN ) ;
				}
				else 
				{
					$criterion = $criteria->getNewCriterion( $partner_field_name , $partner_id ) ;
				}	
	
				
				if ( $kaltura_network )
				{
					$display_in_search_field_name = $peer->translateFieldName( "display_in_search" , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
					$criterion2 = $criteria->getNewCriterion( $display_in_search_field_name , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK , Criteria::GREATER_EQUAL );
					$criterion->addOr ( $criterion2 ) ;
				}
				
				$criteria->addAnd( $criterion );
			}
		}
			
		$criteria_filter->enable();
	}

	public static function partnerHasRoles ( $partner_id )
	{

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

		myContentStorage::fullMkdir($path);

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

	public static function shouldModerate ( $partner_id )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner ) return false;
		return $partner->getModerateContent();
	}
	
	// if the host of the partner is false or null or an empty string - ignore it
	public static function getHost ( $partner_id )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner || (! $partner->getHost() ) ) return requestUtils::getRequestHost();
		return $partner->getHost();
	}
	
	
	public static function getCdnHost ( $partner_id, $protocol = 'http' )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( !$partner || (! $partner->getCdnHost() ) ) return requestUtils::getCdnHost($protocol);
		
		$cdnHost = $partner->getCdnHost();
		$cdnHost = preg_replace('/^https?/', $protocol, $cdnHost);
		return $cdnHost;
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
		
		$conversion_profile_id =  $partner->getCurrentConversionProfileType();
		if( !$conversion_profile_id)
		{
			// will return the partner OLD default profile and if not found - the system default profile
			$conversion_profile_id =  $partner->getCurrentConversionProfileType();
			if ( ! $conversion_profile_id )
				$conversion_profile_id =  $partner->getDefConversionProfileType();
		}
		
		return myConversionProfileUtils::getConversionProfile( $partner_id , $conversion_profile_id );
	}
	
	/**
	 * sets the current ConversionProfile for the partner
	 */
	public static function setCurrentConversionProfile ( $partner_id , $conversion_profile )
	{
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		if ( ! $partner ) return null ; // VERY BAD !!	

		$partner_current_conversion_profile = $partner->getCurrentConversionProfileType();

		if ( $conversion_profile->getId() != $partner_current_conversion_profile )
		{
			$partner->setCurrentConversionProfileType( $partner_conv_profile->getId() );
			$partner->save();
		}	
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
		
		KalturaLog::log("getConversionProfile2ForEntry: conversion_profile_2_id [$conversion_profile_2_id]");
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
			
			KalturaLog::log("getConversionProfile2ForEntry: conversion_quality [$conversion_quality]");
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
		
		switch ( $partner->getPartnerPackage() ){
			case PartnerPackages::PARTNER_PACKAGE_20:
			case PartnerPackages::PARTNER_PACKAGE_50:
			case PartnerPackages::PARTNER_PACKAGE_100:
			case PartnerPackages::PARTNER_PACKAGE_250:
			case PartnerPackages::PARTNER_PACKAGE_500:
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
	
	public static function collectPartnerUsageFromDWH($partner, $partnerPackage, $report_date, $data_for_graph = false)
        {
                // reset values:
                $totalStorage = 0;
                $totalTraffic = 0;
                $totalUsage = 0;
                
		$db_config = kConf::get( "reports_db_config" );
		if(!isset($db_config['port']) || $db_config['port'] === null)
			$db_config['port'] = 3306;

		$timeout = isset ( $db_config["timeout"] ) ? $db_config["timeout"] : 40;
		
		ini_set('mysql.connect_timeout', $timeout );
		//$link  = mysql_connect ( $db_config["host"] , $db_config["user"] , $db_config["password"] , null );
	
		//$db_selected =  mysql_select_db ( $db_config["db_name"] , $link );
		$linki = mysqli_connect($db_config["host"], $db_config["user"] , $db_config["password"], $db_config["db_name"], $db_config['port']); // leave these values for production deployment
		//$linki = mysqli_connect($db_config["host"], $db_config["user"] , $db_config["password"], $db_config["db_name"]);

                /**
                 * call stored-procedure on DWH that based on daily aggregation will return
                 * all the required data.
                 */
                $query = 'CALL kalturadw.calc_partner_billing_data("'.$report_date.'" , '.$partner->getId().');';

		$result = mysqli_query($linki, $query);
		// Check result
		// This shows the actual query sent to MySQL, and the error. Useful for debugging.
		if (!$result) 
		{
		
		    $message  = 'Invalid query: ' . mysqli_error($linki) . "\n";
		    $message .= 'Whole query: ' . $query;
		    throw new Exception('could not get partner usage from DWH');
		}
			
		$res = array();
	
		while ($row = mysqli_fetch_assoc($result)) 
		{			
			$res[] = $row;
		}

		mysqli_free_result($result);
		mysqli_close($linki);
                if($data_for_graph)
		{
		    return $res;
		}
		
                // according to $partnerPackage['id'], decide which row to take (last date, or full rollup row)
                if ($partnerPackage['id'] == 1) // free package
                {
		    // $res[count($res)-1] => total rollup, always irrelevant
		    // $res[count($res)-2] => specific partner rollup, relevant for free partner
                    $relevant_row = count($res)-2;
                }
                else
                {
		    // $res[count($res)-1] => total rollup, always irrelevant
		    // $res[count($res)-2] => specific partner rollup, relevant for free partner
		    // $res[count($res)-3] => specific partner, last month, relevant for paying partner
                    $relevant_row = count($res)-3;
                }
          
                $totalStorage = $res[$relevant_row]['avg_continuous_aggr_storage_mb']; // MB
                $totalTraffic = $res[$relevant_row]['sum_partner_bandwidth_kb']; // KB
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
	
	public static function doPartnerUsage($partner)
	{
		$should_block_delete_partner = true;
		
		$blocking_days_grace = 7;
		$block_notification_grace = time() - (dateUtils::DAY * $blocking_days_grace);
		$delete_grace = time() -  (dateUtils::DAY * 30);
		
		$packages = new PartnerPackages();
		$partnerPackage = $packages->getPackageDetails($partner->getPartnerPackage());
		
		$report_date = date('Y-m').'-01';
                // We are now working with the DWH and a stored-procedure, and not with record type 6 on partner_activity.
                $report_date = dateUtils::todayOffset(-3);

		list ( $totalStorage , $totalUsage , $totalTraffic ) = myPartnerUtils::collectPartnerUsageFromDWH($partner, $partnerPackage, $report_date);
		$totalUsageGB = $totalUsage/1024/1024; // from KB to GB
		$percent = round( ($totalUsageGB / $partnerPackage['cycle_bw'])*100, 2);

		TRACE("percent (".$partner->getId().") is: $percent");
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
			TRACE("partner ". $partner->getId() ." reached 80% - setting first warning");
				
			/* prepare mail job, and set EightyPercentWarning() to true/date */
			$partner->setEightyPercentWarning(time());
			$partner->setUsageLimitWarning(0);
			$body_params = array ( $partner->getAdminName(), $partnerPackage['cycle_bw'], $mindtouch_notice, round($totalUsageGB, 2), $email_link_hash , $email_link_hash_adOpt);
			myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_PACKAGE_EIGHTY_PERCENT_WARNING, $partner, $body_params);
		}
		elseif ($percent >= 80 &&
			$percent < 100 &&
			$partner->getEightyPercentWarning() &&
			!$partner->getUsageLimitWarning())
		{
			TRACE("passed the 80%, assume notification sent, nothing to do.");
		}
		elseif ($percent < 80 &&
				$partner->getEightyPercentWarning())
		{
			TRACE("partner ". $partner->getId() ." was 80%, now not. clearing warnings");
				
			/* clear getEightyPercentWarning */
			$partner->setEightyPercentWarning(0);
			$partner->setUsageLimitWarning(0);
		}
		elseif ($percent >= 100 &&
				!$partner->getUsageLimitWarning())
		{
			TRACE("partner ". $partner->getId() ." reached 100% - setting second warning");
				
			/* prepare mail job, and set getUsageLimitWarning() date */
			$partner->setUsageLimitWarning(time());
			// if ($partnerPackage['cycle_fee'] == 0) - script always works on free partners anyway
			{
				$body_params = array ( $partner->getAdminName(), $mindtouch_notice, round($totalUsageGB, 2), $email_link_hash , $email_link_hash_adOpt);
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
			TRACE("partner ". $partner->getId() ." reached 100% $blocking_days_grace days ago - sending block email and blocking partner");
				
			/* send block email and block partner */
			$body_params = array ( $partner->getAdminName(), $mindtouch_notice, round($totalUsageGB, 2), $email_link_hash , $email_link_hash_adOpt);
			myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_PACKAGE_LIMIT_WARNING_2, $partner, $body_params);
			if($should_block_delete_partner)
			{
				$partner->setStatus(2);
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
			TRACE("partner ". $partner->getId() ." reached 100% a month ago - deleting partner");
				
			/* delete partner */
			$body_params = array ( $partner->getAdminName() );
			myPartnerUtils::notifiyPartner(myPartnerUtils::KALTURA_DELETE_ACCOUNT, $partner, $body_params);
			if($should_block_delete_partner)
			{
				$partner->setStatus(0);
			}
		}
		elseif($percent < 80 && ($partner->getUsageLimitWarning() || $partner->getEightyPercentWarning()))
		{
			TRACE("partner ". $partner->getId() ." OK");
			// PARTNER OK 
			// resetting status and warnings should only be done manually
			//$partner->setStatus(1);
			$partner->setEightyPercentWarning(0);
			$partner->setUsageLimitWarning(0);
			
		}
		$partner->save();		
	}
		
	public static function getPartnerUsageGraph( $year , $month , $partner , $resolution = 'days')
	{
		if (!$resolution) $resolution = 'days';
		
		$start_date = $year .'-'. (($month)? $month: '01') .'-01';
		
		switch ( $resolution )
		{
			case 'weeks':
			case 'days':
				$end_date = $year .'-'. ($month + 1) .'-01';
				$end_date_filter = Criteria::LESS_THAN;
				break;
			
			case 'months':
				$start_date = $year.'-'.'01-01';
				if ((int)date('Y') == $year)
				{
					$end_date = date('Y-m-d');
					$end_date_filter = Criteria::LESS_EQUAL;
				}
				else 
				{
					$end_date = ((int)$year + 1).'-01-01';
					$end_date_filter = Criteria::LESS_THAN;
				}
				break;
		}
		
		$c = new Criteria();
		$c->addAnd ( PartnerActivityPeer::PARTNER_ID , $partner->getId() );
		$c->addAnd ( PartnerActivityPeer::ACTIVITY_DATE, $start_date, Criteria::GREATER_EQUAL );
		$c->addAnd ( PartnerActivityPeer::ACTIVITY_DATE, $end_date, $end_date_filter );
		if ($resolution != 'months')
		{
			$c->addAnd ( PartnerActivityPeer::ACTIVITY , PartnerActivity::PARTNER_ACTIVITY_TRAFFIC );
			$activity = PartnerActivityPeer::doSelect( $c );
			$graph_points['line'] = myPartnerUtils::daily_activity_to_graph($activity, $resolution, $start_date);
		}
		else
		{
			$activity = myPartnerUtils::collectPartnerUsageFromDWH($partner, 1, $end_date, true );
			$graph_points['line'] = myPartnerUtils::year_activity_to_graph($activity, $year);
		}
		$strGraphLine = '';

		// sort array by keys
		ksort($graph_points['line']);
		foreach($graph_points['line'] as $point => $usage) {
			$strGraphLine .= (int)$point.','.$usage.';';
		}
		$graph_points['line'] = $strGraphLine;
		
		return $graph_points;
	}
	
	public static function year_activity_to_graph($act, $requested_year)
	{
		$points = array();
		foreach($act as $activity)
		{
			$year = floor($activity['month_id']/100);
			$month = $activity['month_id'] - ($year*100);
			if($requested_year != $year)
				continue;
			$points[(int)$month] = round($activity['sum_partner_bandwidth_kb']/1024); // Amount2 is in KB, converting to MB
		}

		// pad empty months value with value of 0
		for($i=1;$i<=12;$i++) 
		{
			if (!isset($points[$i]))
			{
				$points[$i] = 0;
			}
		}
		return $points;
	}		

	
	public static function daily_activity_to_graph($act, $res, $start_date)
	{
		$points = array();
		foreach ($act as $row)
		{
			$date = explode('/', $row->getActivityDate()); // expected output m/d/Y
			if (!isset($points[(int)$date[1]]))
			{
				$points[(int)$date[1]] = 0;
			}
			$points[(int)$date[1]] += round(($row->getAmount()/1024)); // normalize to MB
		}
	
		// pad empty array cells with 0 traffic
		$days_in_month = date('t', (int)strtotime($start_date));
		for($i=1;$i<=$days_in_month;$i++) 
		{
			if(!isset($points[$i])) 
			{
				$points[$i] = 0;
			}
		}
		return $points;
 	}
 	
 	public static function copyTemplateContent(Partner $fromPartner, Partner $toPartner)
 	{
 		$toPartner->setEnabledPlugins($fromPartner->getEnabledPlugins());
 		$toPartner->setEnabledServices($fromPartner->getEnabledServices());
 		$toPartner->setEnableAnalyticsTab($fromPartner->getEnableAnalyticsTab());
 		$toPartner->setEnableSilverLight($fromPartner->getEnableSilverLight());
 		$toPartner->setEnableVast($fromPartner->getEnableVast());
 		$toPartner->setEnable508Players($fromPartner->getEnable508Players());
 		$toPartner->setLiveStreamEnabled($fromPartner->getLiveStreamEnabled());
 		$toPartner->save();
 		
 		kEventsManager::raiseEvent(new kObjectCopiedEvent($fromPartner, $toPartner));
 		
 		self::copyAccessControls($fromPartner, $toPartner);
 		self::copyConversionProfiles($fromPartner, $toPartner);
		
 		self::copyEntriesByType($fromPartner, $toPartner, entry::ENTRY_TYPE_MEDIACLIP);
 		self::copyEntriesByType($fromPartner, $toPartner, entry::ENTRY_TYPE_PLAYLIST);
 		
 		self::copyUiConfsByType($fromPartner, $toPartner, uiConf::UI_CONF_TYPE_WIDGET);
 		self::copyUiConfsByType($fromPartner, $toPartner, uiConf::UI_CONF_TYPE_KDP3);
 	}
 	
 	public static function copyEntriesByType(Partner $fromPartner, Partner $toPartner, $entryType)
 	{
 		KalturaLog::log("copyEntriesByType - Copying entries from partner [".$fromPartner->getId()."] to partner [".$toPartner->getId()."] with type [".$entryType."]");
 		entryPeer::setUseCriteriaFilter ( false );
 		$c = new Criteria();
 		$c->addAnd(entryPeer::PARTNER_ID, $fromPartner->getId());
 		$c->addAnd(entryPeer::TYPE, $entryType);
 		$c->addAnd(entryPeer::STATUS, entry::ENTRY_STATUS_READY);
 		$c->addDescendingOrderByColumn(entryPeer::CREATED_AT);
 		$entries = entryPeer::doSelect($c);
 		entryPeer::setUseCriteriaFilter ( true );
 		foreach($entries as $entry)
 		{
 			myEntryUtils::copyEntry($entry, $toPartner);
 		}
 	}
 	
 	public static function copyUiConfsByType(Partner $fromPartner, Partner $toPartner, $uiConfType)
 	{
 		KalturaLog::log("copyUiConfsByType - Copying uiconfs from partner [".$fromPartner->getId()."] to partner [".$toPartner->getId()."] with type [".$uiConfType."]");
 		uiConfPeer::setUseCriteriaFilter(false);
 		$c = new Criteria();
 		$c->addAnd(uiConfPeer::PARTNER_ID, $fromPartner->getId());
 		$c->addAnd(uiConfPeer::OBJ_TYPE, $uiConfType);
 		$c->addAnd(uiConfPeer::STATUS, uiConf::UI_CONF_STATUS_READY);
 		$uiConfs = uiConfPeer::doSelect($c);
 		uiConfPeer::setUseCriteriaFilter ( true );
 		foreach($uiConfs as $uiConf)
 		{
 			$newUiConf = $uiConf->cloneToNew(null);
 			$newUiConf->setPartnerId($toPartner->getId());
 			$newUiConf->save();
 			KalturaLog::log("copyUiConfsByType - UIConf [".$newUiConf->getId()."] was created");
 		}
 	}
 	
 	public static function copyConversionProfiles(Partner $fromPartner, Partner $toPartner)
 	{
		$copiedList = array();
		
 		KalturaLog::log("copyConversionProfiles - Copying conversion profiles from partner [".$fromPartner->getId()."] to partner [".$toPartner->getId()."]");
 		
 		$c = new Criteria();
 		$c->add(conversionProfile2Peer::PARTNER_ID, $fromPartner->getId());
 		
 		$conversionProfiles = conversionProfile2Peer::doSelect($c);
 		foreach($conversionProfiles as $conversionProfile)
 		{
 			$newConversionProfile = $conversionProfile->copy();
 			$newConversionProfile->setPartnerId($toPartner->getId());
 			$newConversionProfile->save();
 			
 			KalturaLog::log("copyConversionProfiles - Copied [".$conversionProfile->getId()."], new id is [".$newConversionProfile->getId()."]");
			$copiedList[$conversionProfile->getId()] = $newConversionProfile->getId();
 			
 			$c = new Criteria();
 			$c->add(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, $conversionProfile->getId());
 			$fpcpList = flavorParamsConversionProfilePeer::doSelect($c);
 			foreach($fpcpList as $fpcp)
 			{
 				$flavorParamsId = $fpcp->getFlavorParamsId();
 				$flavorParams = flavorParamsPeer::retrieveByPK($flavorParamsId);
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
 		KalturaLog::log("copyAccessControls - Copying access controls from partner [".$fromPartner->getId()."] to partner [".$toPartner->getId()."]");
 		
 		$c = new Criteria();
 		$c->add(accessControlPeer::PARTNER_ID, $fromPartner->getId());
 		
 		$accessControls = accessControlPeer::doSelect($c);
 		foreach($accessControls as $accessControl)
 		{
 			$newAccessControl = $accessControl->copy();
 			$newAccessControl->setPartnerId($toPartner->getId());
 			$newAccessControl->save();
 			
 			KalturaLog::log("copyAccessControls - Copied [".$accessControl->getId()."], new id is [".$newAccessControl->getId()."]");
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
			KalturaLog::log ( "blockInactivePartner: BLOCK_PARNTER partner [$partnerId] doesnt exist" );
			KExternalErrors::dieError(KExternalErrors::PARTNER_NOT_FOUND);
		}
			
		$status = $partner->getStatus();
		if ($status != Partner::PARTNER_STATUS_ACTIVE)
		{
			KalturaLog::log ( "blockInactivePartner: BLOCK_PARNTER_STATUS partner [$partnerId] status [$status]" );
			KExternalErrors::dieError(KExternalErrors::PARTNER_NOT_ACTIVE);
		}
		
		// take blocked-countries country code from partner custom data
		$blockContries = $partner->getDelivryBlockCountries();
		// if not set on partner custom data - take from kConf
		if(empty($blockContries) || is_null($blockContries))
		{
			$blockContries = kConf::get ("delivery_block_countries" );
		}
		if ($blockContries)
		{
			// check if request is coming from the blocked country - and block if it is
			$blockedCountry = requestUtils::matchIpCountry( $blockContries , $currentCountry );
			if ($blockedCountry)
			{
				KalturaLog::log ( "blockInactivePartner: IP_BLOCK partner [$partnerId] from country [$currentCountry]" );
				KExternalErrors::dieError(KExternalErrors::IP_COUNTRY_BLOCKED);			
			}
		}
	}
}
?>
