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
	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL3);
		
		return $con;
	}
	
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
	public static function getFlavorIdsByProfileId($conversionProfileId, $con = null)
	{
		$criteria = new Criteria();
		$criteria->addSelectColumn(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID);
		$criteria->add(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, $conversionProfileId);

		$stmt = flavorParamsConversionProfilePeer::doSelectStmt($criteria, $con);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
}
