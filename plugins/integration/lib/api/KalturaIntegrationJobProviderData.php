<?php
/**
 * @package plugins.integration
 * @subpackage api.objects
 */
abstract class KalturaIntegrationJobProviderData extends KalturaObject
{
	const EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME = "EXTERNAL_INTEGRATION_SERVICES_ROLE";

	/**
	 * @return string
	 */
	public static function generateKs($partnerId, $entryId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$userSecret = $partner->getSecret();
		
		//setrole:EXTERNAL_INTEGRATION_SERVICES_ROLE,actionslimit:1
		$privileges = kSessionBase::PRIVILEGE_SET_ROLE . ":" . self::EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME;
		$privileges .= "," . kSessionBase::PRIVILEGE_ACTIONS_LIMIT . ":1";
		
		$dcParams = kDataCenterMgr::getCurrentDc();
		$token = $dcParams["secret"];
		$additionalData = md5($entryId . $token);
		
		$ks = "";
		$creationSucces = kSessionUtils::startKSession ($partnerId, $userSecret, "", $ks, 86400, KalturaSessionType::USER, "", $privileges, null,$additionalData);
		if ($creationSucces >= 0 )
				return $ks;
		
		return false;
	}
}
