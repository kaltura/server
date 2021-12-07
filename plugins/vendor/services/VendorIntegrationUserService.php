<?php

/**
 * @service vendorIntegrationUser
 * @package plugins.vendor
 * @subpackage api.services
 */
class VendorIntegrationUserService extends KalturaBaseUserService
{
	/**
	 * Lists user objects that are associated with an account.
	 * Blocked users are listed unless you use a filter to exclude them.
	 * Deleted users are not listed unless you use a filter to include them.
	 *
	 * @action list
	 * @param KalturaVendorIntegrationUserFilter $filter A filter used to exclude specific types of users
	 * @param KalturaFilterPager $pager A limit for the number of records to display on a page
	 * @return KalturaVendorIntegrationUserListResponse The list of user objects
	 */
	public function listAction(KalturaVendorIntegrationUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaVendorIntegrationUserFilter();
		
		if(!$pager)
			$pager = new KalturaFilterPager();
		
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
	
	/**
	 * * Updates an existing user object.
	 * You can also use this action to update the userId.
	 *
	 * @action update
	 * @param string $userId
	 * @param KalturaVendorIntegrationUser $user
	 * @return KalturaVendorIntegrationUser
	 * @throws KalturaAPIException
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