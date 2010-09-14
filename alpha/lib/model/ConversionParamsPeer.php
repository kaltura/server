<?php

/**
 * Subclass for performing query and update operations on the 'conversion_params' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ConversionParamsPeer extends BaseConversionParamsPeer
{
	// TODO - should override kaltura's params with the partner's params for any profile_type
	// -> if there are both params for the profile_type both under partner_id=0 and the profile's partner_id - 
	// prefer the partner's  
	/**
	 * will fetch the best set of conversion params that fit this profile
	 * the link is done using 2 fields -  conv_profile->profileType &  conv_profile->profileTypeSuffix
	 *  matched agains conv_params->profileType 
	 * 'parnter_id' - the partner of the profile
	 * search for the params 
	 * 1 - for this partner_id by conv_profile->profileType . conv_profile->profileTypeSuffix ( a simple concatenation with no separator )
	 * if none found
	 * 2 - for this partner_id by conv_profile->profileType 
	 * if none found 
	 * 3 - for global partner by conv_profile->profileType . conv_profile->profileTypeSuffix
	 * if none found
	 * 4 - for global partner by conv_profile->profileType 
	 * if none found 
	 * 5- default of the system - always exists
	 *
	 * fallback_mode will hold the number of the method chosen 
	 * 
	 * TODO - optimize the fetchng of the params to 1 call for all of the 4 first options - fetch once and sort in the application
	 * for now it's good enough 
	 * @param ConversionProfile $conv_profile
	 * @return unknown
	 */
	public static function retrieveByConversionProfile ( ConversionProfile $conv_profile , &$fallback_mode = null , $display_disabled = false )
	{
		// ASSUME - $conv_profile is not null
		$partner_id =  $conv_profile->getPartnerId();
		
		// #1
		list ( $conversion_params_list , $fallback_mode ) = 
			self::retrieveByPartnerAndPrifileTypeImpl ( 1 , $partner_id , $conv_profile->getProfileType() . $conv_profile->getProfileTypeSuffix() , $display_disabled  );
		
		if ( count ( $conversion_params_list ) ) return $conversion_params_list;
		
		// #2 - ignore the  getProfileTypeSuffix . if it does not exist , ignoring it is the same as #1
		if ( $conv_profile->getProfileTypeSuffix() )
		{
			list ( $conversion_params_list , $fallback_mode ) = 
				self::retrieveByPartnerAndPrifileTypeImpl ( 2 , $partner_id , $conv_profile->getProfileType(), $display_disabled  );
				
			if ( count ( $conversion_params_list ) ) return $conversion_params_list;
		}

		// nothing for the partner - go for ConversionProfile::GLOBAL_PARTNER_PROFILE
		// #3
		list ( $conversion_params_list , $fallback_mode ) = 
			self::retrieveByPartnerAndPrifileTypeImpl ( 3 , ConversionProfile::GLOBAL_PARTNER_PROFILE , $conv_profile->getProfileType() . $conv_profile->getProfileTypeSuffix() , $display_disabled );
		
		if ( count ( $conversion_params_list ) ) return $conversion_params_list;
		
		// #4 - ignore the  getProfileTypeSuffix . if it does not exist , ignoring it is the same as #1
		if ( $conv_profile->getProfileTypeSuffix() )
		{
			list ( $conversion_params_list , $fallback_mode ) = 
				self::retrieveByPartnerAndPrifileTypeImpl ( 4, ConversionProfile::GLOBAL_PARTNER_PROFILE , $conv_profile->getProfileType() , $display_disabled);
				
			if ( count ( $conversion_params_list ) ) return $conversion_params_list;
		}
		
		// #5 - the  system default - MUST always exist in the system
		list ( $conversion_params_list , $fallback_mode ) = 
			self::retrieveByPartnerAndPrifileTypeImpl ( 5, ConversionProfile::GLOBAL_PARTNER_PROFILE , ConversionProfile::DEFAULT_COVERSION_PROFILE_TYPE , $display_disabled );
		return $conversion_params_list;
	}
	
	// this is an internal function to save some work for retrieveByConversionProfile - it returns a tuple of the list and an array ( $fallback_mode , $partner_id , $profile_type )
	// this structure can be used for bettern understanding why a set of params was selected 
	private static function retrieveByPartnerAndPrifileTypeImpl ( $fallback_mode , $partner_id , $profile_type , $display_disabled = false)
	{
		$c = new Criteria();
		$c->addAnd ( self::PROFILE_TYPE , $profile_type );
		$c->addAnd ( self::PARTNER_ID , $partner_id  );	
		if ( ! $display_disabled )
			$c->addAnd ( self::ENABLED , 1   );
		$c->addAscendingOrderByColumn( self::PROFILE_TYPE_INDEX );
		$conversion_params_list =  self::doSelect( $c );
		return array ( $conversion_params_list , array ( "mode" => $fallback_mode , "partner_id" => $partner_id , "profile_type" => $profile_type ) );		
	}
}
