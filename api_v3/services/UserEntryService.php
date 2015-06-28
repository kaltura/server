<?php

/**
 * @service userEntry
 * @package api
 * @subpackage services
 */
class UserEntryService extends KalturaBaseService {

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
	}

	/**
	 * Adds a user_entry to the Kaltura DB.
	 *
	 * @action add
	 * @param KalturaUserEntry $userEntry
	 * @return KalturaUserEntry
	 */
	public function addAction(KalturaUserEntry $userEntry)
	{
		$userEntry->userId = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $userEntry->userId);
		$dbUserEntry = $userEntry->toInsertableObject(null, array('type'));
		$userId = $userEntry->userId;
		if ($userId == 0)
		{
			$userId = kCurrentContext::$ks_kuser;
		}
		$dbUserEntry->save();

		$userEntry->fromObject($dbUserEntry, $this->getResponseProfile());

		return $userEntry;
	}

	/**
	 *
	 * @action update
	 * @param int $id
	 * @param KalturaUserEntry $userEntry
	 * @throws KalturaAPIException
	 */
	public function updateAction($id, KalturaUserEntry $userEntry)
	{
		$dbUserEntry = UserEntryPeer::retrieveByPK($id);
		if (!$dbUserEntry)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);

		$dbUserEntry = $userEntry->toUpdatableObject($dbUserEntry);
		$dbUserEntry->save();
	}

	/**
	 * @action delete
	 * @param int $id
	 * @return KalturaUserEntry The deleted UserEntry object
 	 * @throws KalturaAPIException
	 */
	public function deleteAction($id)
	{
		$dbUserEntry = UserEntryPeer::retrieveByPK($id);
		if (!$dbUserEntry)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		$dbUserEntry->setStatus(KalturaUserEntryStatus::DELETED);
		$dbUserEntry->save();

		$userEntry = KalturaUserEntry::getInstanceByType($dbUserEntry->getType());
		$userEntry->fromObject($dbUserEntry, $this->getResponseProfile());

		return $userEntry;

	}

	/**
	 * @action list
	 * @param KalturaUserEntryFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaUserEntryListResponse
	 */
	public function listAction(KalturaUserEntryFilter $filter, KalturaFilterPager $pager = null)
	{
		if (!$filter)
		{
			$filter = new KalturaUserEntryFilter();
		}
		if (!$pager)
		{
			$pager = new KalturaFilterPager();
		}
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}

	/**
	 * @action get
	 * @param string $id
	 * @return KalturaUserEntry
	 * @throws KalturaAPIException
	 */
	public function getAction($id)
	{
		$dbUserEntry = UserEntryPeer::retrieveByPK( $id );
		if(!$dbUserEntry)
			throw new KalturaAPIException(KalturaErrors::USER_ENTRY_NOT_FOUND, $id);

		$userEntry = KalturaUserEntry::getInstanceByType($dbUserEntry->getType());
		if (!$userEntry)
			return null;
		$userEntry->fromObject($dbUserEntry);
		return $userEntry;
	}

}