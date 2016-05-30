<?php
/**
 * @package plugins.varConsole
 */
class VarConsolePlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions
{
    const PLUGIN_NAME = "varConsole";

	/* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
    public static function getPluginName ()
    {    
        return self::PLUGIN_NAME;
    }


	/* (non-PHPdoc)
     * @see IKalturaServices::getServicesMap()
     */
    public static function getServicesMap ()
    {
        $map = array(
			'varConsole' => 'VarConsoleService',
		);
		
		return $map;
    }
    
    /* (non-PHPdoc)
     * @see IKalturaPermissions::isAllowedPartner($partnerId)
     */
    public static function isAllowedPartner($partnerId)
    {
        $partner = PartnerPeer::retrieveByPK($partnerId);
		
		return $partner->getEnabledService(KalturaPermissionName::FEATURE_VAR_CONSOLE_LOGIN);
    }

}