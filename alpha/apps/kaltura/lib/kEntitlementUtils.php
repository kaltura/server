<?php
/**
 * kEntitlementUtils is all utils needed for entitlement use cases.
 * @package Core
 * @subpackage utils
 *
 */
class kEntitlementUtils 
{

	protected static $entitlementScope = false;  
	
	
	public static function getEntitlementScope()
	{
		return self::$entitlementScope;
	}
	
	/**
	 * Returns true if kuser or current kuser is entitled to entryId
	 * @param string $entryId
	 * @param int $kuser
	 * @return bool
	 */
	public static function isEntryEntitled($entryId, $kuserId = null)
	{
		//TODO
	} 	

	/**
	 * Returns true if kuser or current kuser is entitled to assign entry to categoryId
	 * @param int $categoryId
	 * @param int $kuser
	 * @return bool
	 */
	public static function validateEntryAssignToCategory($categoryId, $kuserId = null)
	{ 
		//TODO
	}
	
	/**
	 * Set Entitlement scope - if entitelement is enabled \ disabled in this session
	 * @param int $categoryId
	 * @param int $kuser
	 * @return bool
	 */
	public static function initEntitlementScope()
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id; 
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner)
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID);
			
		if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENTITLEMENT, $partnerId))
			return;		
		
		$partnerDefaultEntitlementScope = $partner->getDefaultEntitlementScope();
		
		// default entitlement scope is false - disable.
		if(is_null($partnerDefaultEntitlementScope))
			$partnerDefaultEntitlementScope = false;
		
		$ksString = kCurrentContext::$ks ? kCurrentContext::$ks : null;
		if ($ksString == '') // for actions with no KS or when creating ks.
			return;
		
		$ks = ks::fromSecureString($ksString);
		
		if (!$partnerDefaultEntitlementScope)
		{
			self::$entitlementScope = false;
			$enableEntitlement = $ks->getEnableEntitlement();
			if ($enableEntitlement)
				self::$entitlementScope = true;
		}
		else
		{
			self::$entitlementScope = true;
			$enableEntitlement = $ks->getDisableEntitlement();
			if ($enableEntitlement)
				self::$entitlementScope = false;
		}
	}
	
	
}
