<?php
/**
 * Base class for entry server node
 *
 * @service entryServerNode
 * @package api
 * @subpackage services
 */
class EntryServerNodeService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass("entry");
	}

	/**
	 * Adds a entry_user_node to the Kaltura DB.
	 *
	 * @action add
	 * @param KalturaEntryServerNode $entryServerNode
	 * @return KalturaEntryServerNode
	 */
	public function addAction(KalturaEntryServerNode $entryServerNode)
	{
		$dbUserEntry = $entryServerNode->toInsertableObject(null, null);
		$dbUserEntry->save();

		$entryServerNode->fromObject($dbUserEntry, $this->getResponseProfile());

		return $entryServerNode;
	}

	/**
	 *
	 * @action update
	 * @param int $id
	 * @param KalturaEntryServerNode $entryServerNode
	 * @throws KalturaAPIException
	 */
	public function updateAction($id, KalturaEntryServerNode $entryServerNode)
	{
		$dbUserEntry = EntryServerNodePeer::retrieveByPK($id);
		if (!$dbUserEntry)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);

		$dbUserEntry = $entryServerNode->toUpdatableObject($dbUserEntry);
		$dbUserEntry->save();
	}

	/**
	 * Deletes the row in the database
	 * @action delete
	 * @param int $id
	 * @throws KalturaAPIException
	 */
	public function deleteAction($id)
	{
		$dbEntryServerNode = EntryServerNodePeer::retrieveByPK($id);
		if (!$dbEntryServerNode)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		$dbEntryServerNode->delete();

	}

	/**
	 * @action list
	 * @param KalturaEntryServerNodeFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaEntryServerNodeListResponse
	 */
	public function listAction(KalturaEntryServerNodeFilter $filter, KalturaFilterPager $pager = null)
	{
		if (!$filter)
		{
			$filter = new KalturaEntryServerNodeFilter();
		}
		if (!$pager)
		{
			$pager = new KalturaFilterPager();
		}
		// return empty list when userId was not given
		if ( $this->getKs() && !$this->getKs()->isAdmin() && !kCurrentContext::$ks_uid ) {
			return new KalturaEntryServerNodeListResponse();
		}
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}

	/**
	 * @action get
	 * @param string $id
	 * @return KalturaEntryServerNode
	 * @throws KalturaAPIException
	 */
	public function getAction($id)
	{
		// TODO - I think this function needs change
		$dbEntryServerNode = EntryServerNodePeer::retrieveByPK( $id );
		if(!$dbEntryServerNode)
			throw new KalturaAPIException(KalturaErrors::USER_ENTRY_NOT_FOUND, $id);

		$entryServerNode = EntryServerNodePeer::getInstanceByType($dbEntryServerNode->getType());
		if (!$entryServerNode)
			return null;
		$entryServerNode->fromObject($dbEntryServerNode);
		return $entryServerNode;
	}

}