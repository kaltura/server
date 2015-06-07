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
		$dbUserEntry = $userEntry->toInsertableObject(null, array('type'));
		$userId = $userEntry->userId;
		if ($userId == 0)
		{
			$userId = kCurrentContext::$ks_kuser;
		}
		/*$dbUserEntry->setKuserId($userId);
		$dbUserEntry->setType($userEntry->type);
		$dbUserEntry->setStatus(KalturaUserEntryStatus::ACTIVE);*/
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

		return $filter->getListResponse(new KalturaFilterPager(), $this->getResponseProfile());
	}

}