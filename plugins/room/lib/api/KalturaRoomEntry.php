<?php

/**
 * @package plugins.room
 * @subpackage api.objects
 */
class KalturaRoomEntry extends KalturaBaseEntry
{

	/**
	 * @filter eq
	 * @var KalturaRoomType
	 */
	public $roomType;

	/**
	 * The entryId of the broadcast that the room streaming to
	 * @var string
	 */
	public $broadcastEntryId;

	/**
	 * The entryId of the room where settings will be taken from
	 * @var string
	 */
	public $templateRoomEntryId;

	private static $map_between_objects = array(
		'roomType',
		'broadcastEntryId',
		'templateRoomEntryId'
	);

	public function __construct()
	{
		$this->type = RoomPlugin::getApiValue(RoomEntryType::ROOM);
	}

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new RoomEntry();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('roomType');
		$this->validateTemplateRoomEntry();
		return parent::validateForInsert($propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateTemplateRoomEntry();
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	public function validateTemplateRoomEntry()
	{
		if (!isset($this->templateRoomEntryId) || $this->templateRoomEntryId === '')
		{
			return;
		}
		$entry = $this->retrieveTemplateRoomEntry($this->templateRoomEntryId);
		if (!$entry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->templateRoomEntryId);
		}
		if ($entry->getType() !== RoomPlugin::getEntryTypeCoreValue(RoomEntryType::ROOM))
		{
			throw new KalturaAPIException(APIErrors::INVALID_FIELD_VALUE, 'templateRoomEntryId');
		}
	}

	private function retrieveTemplateRoomEntry($entryId)
	{
		$c = new Criteria();
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id :  kCurrentContext::$ks_partner_id;
		entryPeer::setUseCriteriaFilter (false);
		// allow setting entry of the "global" partner
		$allowedPids = array($partnerId, Partner::KME_PARTNER_ID);
		$c->addAnd(entryPeer::PARTNER_ID, $allowedPids, Criteria::IN);
		$c->addAnd (entryPeer::STATUS, entryStatus::DELETED, Criteria::NOT_EQUAL);
		$c->addAnd (entryPeer::ID, $entryId, Criteria::EQUAL);
		$entry = entryPeer::doSelectOne($c);
		entryPeer::setUseCriteriaFilter (true);
		return $entry;
	}


}