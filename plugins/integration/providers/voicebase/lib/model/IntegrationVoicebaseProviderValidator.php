<?php
/**
 * @package plugins.voicebase
 * @subpackage model
 */
class IntegrationVoicebaseProviderValidator implements IIntegrationProvider
{
	/* (non-PHPdoc)
	 * @see IIntegrationProvider::getPermissions($partnerId)
	 */
	public static function getPermissions($partnerId)
	{
		$permissionNames = array(VoicebasePlugin::PARTNER_LEVEL_PERMISSION_NAME);
		return PermissionPeer::retrievePartnerLevelPermissions($partnerId, null, $permissionNames);
	}
	
	
	/* (non-PHPdoc)
	 * @see IIntegrationProvider::validateKs($ks, $job)
	 */
	public static function validateKs(ks $ks, $job)
	{
		$data = $job->getData();
		$providerData = $data->getProviderData();
		$entryId = $providerData->getEntryId();
	
		$dcParams = kDataCenterMgr::getCurrentDc();
				$token = $dcParams["secret"];
	
		$createdString = md5($entryId . $token);
	
		if($createdString == $ks->additional_data)
			return true;
	
		return false;
	}
}
