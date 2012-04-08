<?php
class LikePlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions
{
    const PLUGIN_NAME = "like";
    
	/* (non-PHPdoc)
     * @see IKalturaServices::getServicesMap()
     */
    public static function getServicesMap ()
    {
        $map = array(
			'like' => 'LikeService',
		);
		return $map;
    }

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		
		return $partner->getEnabledService(KalturaPermissionName::FEATURE_LIKE);
	}
	

	/* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
    public static function getPluginName ()
    {
        return self::PLUGIN_NAME;
    }

    
}