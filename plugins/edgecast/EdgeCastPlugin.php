<?php
/**
 * @package plugins.edgeCast
 */
class EdgeCastPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'edgeCast';
	const EDGECAST_FLOW_MANAGER = 'kEdgeCastFlowManager';
	const PARTNER_CUSTOM_DATA_FIELD_EDGECAST_PARAMS = 'edgeCastParams';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);		
	}
			
	public static function getEventConsumers(){
		return array(self::EDGECAST_FLOW_MANAGER);
	}
	
	/**
	 * @param Partner $partner
	 * @return kEdgeCastParams
	 */
	public static function getEdgeCastParams($partner)
	{
		return $partner->getFromCustomData(self::getPluginName().'_'.self::PARTNER_CUSTOM_DATA_FIELD_EDGECAST_PARAMS);
	}
	
	/**
	 * @param Partner $partner
	 * @param kEdgeCastParams $edgeCastParams
	 */
	public static function setEdgeCastParams($partner, $edgeCastParams)
	{		
		$partner->putInCustomData(self::getPluginName().'_'.self::PARTNER_CUSTOM_DATA_FIELD_EDGECAST_PARAMS, $edgeCastParams);
	}	
	
}
