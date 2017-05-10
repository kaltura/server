<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params_conversion_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
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
	
	/**
	 * 
	 * @param int $conversionProfileId
	 * @param $con
	 * 
	 * @return array
	 */
	public static function retrieveByConversionProfile($conversionProfileId, $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, $conversionProfileId);

		return flavorParamsConversionProfilePeer::doSelect($criteria, $con);
	}
	
	/**
	 * 
	 * @param int $conversionProfileId
	 * @param $con
	 * 
	 * @return array
	 */
	public static function getFlavorIdsByProfileId($conversionProfileId, $con = null)
	{
		$criteria = new Criteria();
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID);
		$criteria->add(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, $conversionProfileId);

		$stmt = flavorParamsConversionProfilePeer::doSelectStmt($criteria, $con);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("flavorParamsConversionProfile:flavorParamsId=%s,conversionProfileId=%s", self::FLAVOR_PARAMS_ID, self::CONVERSION_PROFILE_ID), array("flavorParamsConversionProfile:conversionProfileId=%s", self::CONVERSION_PROFILE_ID));		
	}

	public static function getTempFlavorsParams($entryId)
	{
		$conversionProfile = myPartnerUtils::getConversionProfile2ForEntry($entryId);
		if(!$conversionProfile)
			return null;

		$criteria = new Criteria();
		$criteria->add(self::CONVERSION_PROFILE_ID, $conversionProfile->getId());
		$criteria->add(self::DELETE_POLICY, AssetParamsDeletePolicy::DELETE);
		$tempFlavorsParams = self::doSelect($criteria);
		return $tempFlavorsParams;
	}
}
