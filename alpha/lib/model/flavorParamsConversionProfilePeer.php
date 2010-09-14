<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params_conversion_profile' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorParamsConversionProfilePeer extends BaseflavorParamsConversionProfilePeer
{
	/**
	 * 
	 * @param int $flavorParamsId
	 * @param int $conversionProfileId
	 * @param $con
	 * 
	 * @return flavorParamsConversionProfile
	 */
	public static function retrieveByFlavorParamsAndConversionProfile($flavorParamsId, $conversionProfileId, $con = null)
	{
		$criteria = new Criteria();

		$criteria->add(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, $flavorParamsId);
		$criteria->add(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, $conversionProfileId);

		return flavorParamsConversionProfilePeer::doSelectOne($criteria, $con);
	}
}
