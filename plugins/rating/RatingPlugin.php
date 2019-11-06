<?php
/**
 * Enable user-conscious 5-star rating
 * @package plugins.rating
 */

class RatingPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices
{
	const PLUGIN_NAME = "rating";
	
	/* (non-PHPdoc)
    * @see IKalturaServices::getServicesMap()
    */
	public static function getServicesMap()
	{
		$map = array(
			'like' => 'RatingService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		
		return $partner->getEnabledService(KalturaPermissionName::FEATURE_RATING);
	}
	
	
	/* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
}