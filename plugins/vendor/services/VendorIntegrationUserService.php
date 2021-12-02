<?php

/**
 * @service vendorIntegrationUser
 * @package plugins.vendor
 * @subpackage api.services
 */
class VendorIntegrationUserService extends KalturaBaseUserService
{

	/**
	 * Updates an existing user object.
	 * You can also use this action to update the userId.
	 *
	 * @param $userId
	 * @param KalturaVendorIntegrationUser $user
	 * @return KalturaVendorIntegrationUser
	 * @throws Exception
	 */
	public function updateAction($userId, KalturaVendorIntegrationUser $user)
	{
		$dbUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
		if (!$dbUser)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
		}

		$dbUser = $user->toUpdatableObject($dbUser);
		$dbUser->save();

		$user->fromObject($dbUser, $this->getResponseProfile());

		return $user;
	}

}