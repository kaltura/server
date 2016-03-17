<?php

/**
 * @package Core
 * @subpackage externalServices
 */
abstract class oauth2Action extends sfAction{

	const EXPIRY_SECONDS = 1800; // 30 minutes

	private function generateKs($partnerId, $additionalData, $privileges)
	{
		$partner = $this->getPartner($partnerId);
		$limitedKs = '';
		$result = kSessionUtils::startKSession($partnerId, $partner->getAdminSecret(), '', $limitedKs, self::EXPIRY_SECONDS, kSessionBase::SESSION_TYPE_ADMIN, '', $privileges, null, $additionalData);
		if ($result < 0)
			throw new Exception('Failed to create limited session for partner '.$partnerId);

		return $limitedKs;
	}

	protected function generateTimeLimitedKsWithData($partnerId, $stateData)
	{
		$privileges = kSessionBase::PRIVILEGE_ACTIONS_LIMIT.':0';
		$additionalData =  json_encode($stateData);
		return $this->generateKs($partnerId, $additionalData, $privileges);
	}

	protected function generateTimeLimitedKs($partnerId)
	{
		return $this->generateKs($partnerId, null, null);
	}

	protected function getPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (is_null($partner))
			throw new Exception('Partner id '. $partnerId.' not found');

		return $partner;
	}

	protected function processKs($ksStr, $requiredPermission = null)
	{
		try
		{
			kCurrentContext::initKsPartnerUser($ksStr);
		}
		catch(Exception $ex)
		{
			KalturaLog::err($ex);
			return false;
		}

		if (kCurrentContext::$ks_object->type != ks::SESSION_TYPE_ADMIN)
		{
			KalturaLog::err('Ks is not admin');
			return false;
		}

		try
		{
			kPermissionManager::init(kConf::get('enable_cache'));
		}
		catch(Exception $ex)
		{
			if (strpos($ex->getCode(), 'INVALID_ACTIONS_LIMIT') === false) // allow using limited ks
			{
				KalturaLog::err($ex);
				return false;
			}
		}
		if ($requiredPermission)
		{
			if (!kPermissionManager::isPermitted(PermissionName::ADMIN_PUBLISHER_MANAGE))
			{
				KalturaLog::err('Ks is missing "ADMIN_PUBLISHER_MANAGE" permission');
				return false;
			}
		}

		return true;
	}

}