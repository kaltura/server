<?php
/**
 * @package plugins.room
 * @subpackage api.filters
 */
class KalturaRoomEntryFilter extends KalturaRoomEntryBaseFilter
{
	public function __construct()
	{
		$this->typeEqual = RoomPlugin::getEntryTypeCoreValue(RoomEntryType::ROOM);
	}

	public function toObject($object_to_fill = null, $skip = array())
	{
		/* @var $object_to_fill entryFilter */
		if (is_null($object_to_fill))
		{
			$object_to_fill = $this->getCoreFilter();
		}

		if ($this->roomTypeEqual)
		{
			$object_to_fill->fields['_like_plugins_data'] = RoomPlugin::getRoomTypeSearchData(kCurrentContext::getCurrentPartnerId(),
				$this->roomTypeEqual);
			$this->roomTypeEqual = null;
		}

		return parent::toObject($object_to_fill, $skip);
	}

	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager);

		$newList = KalturaRoomEntryArray::fromDbArray($list, $responseProfile);
		$response = new KalturaRoomEntryListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;

		return $response;
	}

}
