<?php
/**
 * @service room
 * @package plugins.room
 * @subpackage api.services
 */
class RoomService extends KalturaEntryService
{
	/**
	 *
	 * @action add
	 * @param KalturaRoomEntry $entry
	 * @return KalturaRoomEntry
	 */
	function addAction(KalturaRoomEntry $entry)
	{

		$dbEntry = parent::add($entry);
		$dbEntry->setStatus(entryStatus::NO_CONTENT);
		$dbEntry->save();

		$trackEntry = new TrackEntry();
		$trackEntry->setEntryId($dbEntry->getId());
		$trackEntry->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY);
		$trackEntry->setDescription(__METHOD__ . ":" . __LINE__ . "::ENTRY_ROOM");
		TrackEntry::addTrackEntry($trackEntry);

		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry, $dbEntry->getPartnerId(), null, null, null, $dbEntry->getId());

		$entry->fromObject($dbEntry, $this->getResponseProfile());
		return $entry;
	}

	/**
	 *
	 * @action get
	 * @param string $roomId
	 * @return KalturaRoomEntry
	 * @throws KalturaAPIException
	 */
	function getAction($roomId)
	{
		return $this->getEntry($roomId, -1, RoomPlugin::getEntryTypeCoreValue(RoomEntryType::ROOM));
	}

	/**
	 *
	 * @action update
	 * @param string $roomId
	 * @param KalturaRoomEntry $room
	 * @return KalturaRoomEntry
	 *
	 * @validateUser entry id edit
	 * @throws KalturaAPIException
	 */
	function updateAction($roomId, KalturaRoomEntry $room)
	{
		return $this->updateEntry($roomId, $room, RoomPlugin::getEntryTypeCoreValue(RoomEntryType::ROOM));
	}

	/**
	 *
	 * @action delete
	 * @param string $roomId
	 * @throws KalturaAPIException
	 */
	function deleteAction($roomId)
	{
		$this->deleteEntry($roomId, RoomPlugin::getEntryTypeCoreValue(RoomEntryType::ROOM));
	}

	/**
	 *
	 * @action list
	 * @param KalturaRoomEntryFilter|null $filter
	 * @param KalturaFilterPager|null $pager
	 * @return KalturaRoomEntryListResponse
	 */
	function listAction(KalturaRoomEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
		{
			$filter = new KalturaRoomEntryFilter();
		}

		list($list, $totalCount) = parent::listEntriesByFilter($filter, $pager);

		$response = new KalturaRoomEntryListResponse();
		$response->objects = KalturaRoomEntryArray::fromDbArray($list, $this->getResponseProfile());
		$response->totalCount = $totalCount;
		return $response;
	}

	/**
	 *
	 * @action createRecordedEntry
	 * @param string $roomEntryId
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $cloneEntryId
	 * @return KalturaMediaEntry the new recorded entry created
	 */
	public function createRecordedEntryAction(string $roomEntryId, KalturaMediaEntry $mediaEntry = null, $cloneEntryId = null)
	{
		$dbRoomEntry = entryPeer::retrieveByPK($roomEntryId);
		if (!$dbRoomEntry || !($dbRoomEntry instanceof RoomEntry))
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $roomEntryId);
		}
		if ((!$mediaEntry && !$cloneEntryId) || ($mediaEntry && $cloneEntryId))
		{
			throw new KalturaAPIException( KalturaErrors::PROPERTY_VALIDATION_ALL_MUST_BE_NULL_BUT_ONE, 'mediaEntry / cloneEntryId');
		}
		if ($cloneEntryId)
		{
			$cloneEntry = entryPeer::retrieveByPK($cloneEntryId);
			if (!$cloneEntry)
			{
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $cloneEntryId);
			}
			$dbEntry = myEntryUtils::copyEntry($cloneEntry, $this->getPartner(), array());
		}
		else
		{
			$dbEntry = $this->prepareEntryForInsert($mediaEntry);
		}
		$dbEntry->setRootEntryId($roomEntryId);
		$dbEntry->save();
		$recordedEntry = new KalturaMediaEntry();
		$recordedEntry->fromObject($dbEntry);
		return $recordedEntry;
	}

}
