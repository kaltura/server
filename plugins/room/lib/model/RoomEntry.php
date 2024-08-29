<?php
/**
 * @package plugins.room
 * @subpackage model
 */
class RoomEntry extends entry
{

	const CUSTOM_DATA_ROOM_TYPE = 'roomType';
	const CUSTOM_DATA_BROADCAST_ENTRY_ID = 'broadcastEntryId';
	const CUSTOM_DATA_TEMPLATE_ROOM_ENTRY_ID = 'templateRoomEntryId';

	public function getRoomType()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ROOM_TYPE);
	}

	public function getBroadcastEntryId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_BROADCAST_ENTRY_ID);
	}

    public function getTemplateRoomEntryId()
    {
        return $this->getFromCustomData(self::CUSTOM_DATA_TEMPLATE_ROOM_ENTRY_ID);
    }

	public function setRoomType($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ROOM_TYPE, $v);
	}

	public function setBroadcastEntryId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_BROADCAST_ENTRY_ID, $v);
	}

	public function setTemplateRoomEntryId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_TEMPLATE_ROOM_ENTRY_ID, $v);
	}
	
	public function getObjectParams($params = null)
	{
		$body = array(
			'room_type' => $this->getRoomType(),
			'broadcast_entry_id' => $this->getBroadcastEntryId(),
		);
		
		elasticSearchUtils::cleanEmptyValues($body);
		
		return array_merge(parent::getObjectParams($params), $body);
	}
}