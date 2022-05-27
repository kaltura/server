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
		$this->applyPartnerFilterForClass("entryServerNode");
	}

	/**
	 * Adds a entry_user_node to the Kaltura DB.
	 *
	 * @action add
	 * @param KalturaEntryServerNode $entryServerNode
	 * @return KalturaEntryServerNode
	 */
	private function addAction(KalturaEntryServerNode $entryServerNode)
	{
		$dbEntryServerNode = $this->addNewEntryServerNode($entryServerNode);

		$te = new TrackEntry();
		$te->setEntryId($dbEntryServerNode->getEntryId());
		$te->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY);
		$te->setDescription(__METHOD__ . ":" . __LINE__ . "::" . $dbEntryServerNode->getServerType().":".$dbEntryServerNode->getServerNodeId());
		TrackEntry::addTrackEntry($te);

		$entryServerNode = KalturaEntryServerNode::getInstance($dbEntryServerNode, $this->getResponseProfile());
		return $entryServerNode;

	}

	private function addNewEntryServerNode(KalturaEntryServerNode $entryServerNode)
	{
		$dbEntryServerNode = $entryServerNode->toInsertableObject();
		/* @var $dbEntryServerNode EntryServerNode */
		$dbEntryServerNode->setPartnerId($this->getPartnerId());
		$dbEntryServerNode->setStatus(EntryServerNodeStatus::STOPPED);
		$dbEntryServerNode->save();

		return $dbEntryServerNode;
	}

	/**
	 *
	 * @action update
	 * @param bigint $id
	 * @param KalturaEntryServerNode $entryServerNode
	 * @return KalturaEntryServerNode|null|object
	 * @throws KalturaAPIException
	 */
	public function updateAction($id, KalturaEntryServerNode $entryServerNode)
	{
		$dbEntryServerNode = EntryServerNodePeer::retrieveByPK($id);
		if (!$dbEntryServerNode)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);

		$dbEntryServerNode = $entryServerNode->toUpdatableObject($dbEntryServerNode);
		$dbEntryServerNode->save();

		$entryServerNode = KalturaEntryServerNode::getInstance($dbEntryServerNode, $this->getResponseProfile());
		return $entryServerNode;
	}

	/**
	 * Deletes the row in the database
	 * @action delete
	 * @param bigint $id
	 * @throws KalturaAPIException
	 */
	private function deleteAction($id)
	{
		$dbEntryServerNode = EntryServerNodePeer::retrieveByPK($id);
		if (!$dbEntryServerNode)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		$dbEntryServerNode->deleteOrMarkForDeletion();

	}

	/**
	 * @action list
	 * @param KalturaEntryServerNodeFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaEntryServerNodeListResponse
	 */
	public function listAction(KalturaEntryServerNodeFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaEntryServerNodeFilter();
		if (!$pager)
			$pager = new KalturaFilterPager();

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
		$dbEntryServerNode = EntryServerNodePeer::retrieveByPK( $id );
		if(!$dbEntryServerNode)
			throw new KalturaAPIException(KalturaErrors::ENTRY_SERVER_NODE_NOT_FOUND, $id);

		$entryServerNode = KalturaEntryServerNode::getInstance($dbEntryServerNode);
		if (!$entryServerNode)
			return null;
		$entryServerNode->fromObject($dbEntryServerNode);
		return $entryServerNode;
	}
	
	/**
	 * Validates server node still registered on entry
	 *
	 * @action validateRegisteredEntryServerNode
	 * @param bigint $id entry server node id
	 *
	 * @throws KalturaAPIException
	 */
	public function validateRegisteredEntryServerNodeAction($id)
	{
		KalturaResponseCacher::disableCache();
		
		$dbEntryServerNode = EntryServerNodePeer::retrieveByPK( $id );
		if(!$dbEntryServerNode)
			throw new KalturaAPIException(KalturaErrors::ENTRY_SERVER_NODE_NOT_FOUND, $id);
		
		/* @var EntryServerNode $dbEntryServerNode */
		$dbEntryServerNode->validateEntryServerNode();
	}

	/**
	 * @action updateStatus
	 * @param string $id
	 * @param KalturaEntryServerNodeStatus $status
	 * @return KalturaEntryServerNode
	 * @throws KalturaAPIException
	 */
	public function updateStatusAction($id, $status)
	{
		$dbEntryServerNode = EntryServerNodePeer::retrieveByPK($id);
		if(!$dbEntryServerNode)
			throw new KalturaAPIException(KalturaErrors::ENTRY_SERVER_NODE_NOT_FOUND, $id);

		$dbEntryServerNode->setStatus($status);
		$dbEntryServerNode->save();

		$entryServerNode = KalturaEntryServerNode::getInstance($dbEntryServerNode);
		return $entryServerNode;
	}
}
