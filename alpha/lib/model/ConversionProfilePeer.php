<?php

/**
 * Subclass for performing query and update operations on the 'conversion_profile' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ConversionProfilePeer extends BaseConversionProfilePeer
{
	public static function retrieveByProfileType ( $partner_id , $profile_type )
	{
		$c = new Criteria();
		$c->addAnd ( self::PROFILE_TYPE, $profile_type);
		if ( $partner_id )
		{
			$crit = $c->getNewCriterion( self::PARTNER_ID , ConversionProfile::GLOBAL_PARTNER_PROFILE , Criteria::EQUAL );
			$crit->addOr ( $c->getNewCriterion ( self::PARTNER_ID , $partner_id , Criteria::EQUAL ) );
			$c->add ( $crit );
		}
		else
		{
			$c->add ( self::PARTNER_ID , $partner_id , Criteria::EQUAL  );
		}

		$conv_list =  self::doSelect( $c );
		if ( $conv_list == null ) return null;
		foreach ( $conv_list as $conv )
		{
			// select the first conversion profile that matchs the partner
			 if ( $conv->getPartnerId() == $partner_id  ) return $conv;
		}
		// if no conv_prof found for partner - use the global ones
		return $conv_list[0]; // first profile returned 
	}
	

	/**
	 * fetch the best conversion profile for the partner - depending on the data in the DB only (no hint from the user)
	 */
	public static function retrieveByPartner ( $partner_id  )
	{
		$c = new Criteria();
		$c->addDescendingOrderByColumn ( self::UPDATED_AT ); // make sure the first profile is the most updated
//		$c->addAnd ( self::PROFILE_TYPE, $profile_type);
		if ( $partner_id )
		{
			$crit = $c->getNewCriterion( self::PARTNER_ID , ConversionProfile::GLOBAL_PARTNER_PROFILE , Criteria::EQUAL );
			$crit->addOr ( $c->getNewCriterion ( self::PARTNER_ID , $partner_id , Criteria::EQUAL ) );
			$c->add ( $crit );
		}
		else
		{
			$c->add ( self::PARTNER_ID , $partner_id , Criteria::EQUAL  );
		}

		$conv_list =  self::doSelect( $c );
		if ( $conv_list == null ) return null;
		foreach ( $conv_list as $conv )
		{
			// select the first conversion profile that matchs the partner
			 if ( $conv->getPartnerId() == $partner_id  ) return $conv;
		}
		// if no conv_prof found for partner - use the global ones
		return $conv_list[0]; // first profile returned 
	}
		
	
	public static function retrieveSimilar ( $partner_id  , conversionProfile $conv_profile )
	{
		// don't use tht name when matching 
		$c = new Criteria();
		$c->add ( self::PARTNER_ID , $partner_id );
		$c->add ( self::PROFILE_TYPE , $conv_profile->getProfileType() );
		$c->add ( self::PROFILE_TYPE_SUFFIX , $conv_profile->getProfileTypeSuffix());
		$c->add ( self::COMMERCIAL_TRANSCODER , $conv_profile->getCommercialTranscoder() );
		$c->add ( self::WIDTH , $conv_profile->getWidth() );
		$c->add ( self::HEIGHT , $conv_profile->getHeight() );
		$c->add ( self::ASPECT_RATIO, $conv_profile->getAspectRatio());
		$c->add ( self::BYPASS_FLV, $conv_profile->getBypassFlv());
		
		$existing_conv_profile = self::doSelectOne ( $c );
		
		if ( $existing_conv_profile && ! $existing_conv_profile->getEnabled() )
		{
			// if it's off - rather than creating a new one turning it on 
			$existing_conv_profile->setEnabled ( 1 );
			$existing_conv_profile->save();
		}
		
		return $existing_conv_profile;
		
	}
	
}
