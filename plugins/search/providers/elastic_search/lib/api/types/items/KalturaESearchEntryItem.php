<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchEntryItem extends KalturaESearchItem
{

	const KUSER_ID_THAT_DOESNT_EXIST = -1;

	/**
	 * @var KalturaESearchEntryFieldName
	 */
	public $fieldName;

	private static $map_between_objects = array(
		'fieldName'
	);

	private static $map_dynamic_enum = array(
		KalturaESearchEntryFieldName::ENTRY_TYPE => 'KalturaEntryType',
		KalturaESearchEntryFieldName::ENTRY_SOURCE_TYPE => 'KalturaSourceType',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchEntryItem();

		if(in_array($this->fieldName, array(KalturaESearchEntryFieldName::ENTRY_USER_ID, KalturaESearchEntryFieldName::ENTRY_ENTITLED_USER_EDIT,
			KalturaESearchEntryFieldName::ENTRY_ENTITLED_USER_PUBLISH, KalturaESearchEntryFieldName::ENTRY_CREATOR_ID)))
		{
			$kuserId = $this->getKuserIdfromPuserId($this->searchTerm);
			$this->searchTerm = $kuserId;
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	protected function getItemFieldName()
	{
		return $this->fieldName;
	}

	protected function getDynamicEnumMap()
	{
		return self::$map_dynamic_enum;
	}

	protected function getKuserIdfromPuserId($puserId)
	{
		$kuserId = self::KUSER_ID_THAT_DOESNT_EXIST;
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $puserId, true);
		if($kuser)
			$kuserId = $kuser->getId();

		return $kuserId;
	}

}
