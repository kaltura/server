<?php

/**
 * Subclass for representing a row from the 'conversion_profile' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ConversionProfile extends BaseConversionProfile
{
	const GLOBAL_PARTNER_PROFILE = 0; 					// every profile that belongs to partner_id 0 is global and can be shared between partners
	const CONVERSION_PROFILE_UNKNOWN = -1; 			// kaltura's default conversion profile id
	const DEFAULT_COVERSION_PROFILE_ID = 0; 			// kaltura's default conversion profile id
	const DEFAULT_COVERSION_PROFILE_TYPE = "med"; 		// kaltura's default conversion profile type
	const DEFAULT_DOWNLOAD_PROFILE_ID = 1; 				// kaltura's default download profile id
	const DEFAULT_DOWNLOAD_PROFILE_TYPE = "download"; 	// kaltura's default download profile type
	
	const DEFAULT_TRIAL_COVERSION_PROFILE_TYPE = 1001;	// kaltura's default conversion profile for trial accounts

	
	const CONVERSION_PROFILE_CREATION_MODE_MANUAL = 1;
	const CONVERSION_PROFILE_CREATION_MODE_KMC = 2;
	const CONVERSION_PROFILE_CREATION_MODE_AUTOMATIC = 3;
	
	public function getConversionParams( &$fallback_mode = null )
	{
		$fallback_mode = "";
		return ConversionParamsPeer::retrieveByConversionProfile( $this , $fallback_mode );
	}
}
