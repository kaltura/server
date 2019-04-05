<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchGroupUserItem extends KalturaESearchAbstractUserItem
{
	const KUSER_ID_THAT_DOESNT_EXIST = -1;

	/**
	 * @var KalturaEsearchGroupUserFieldName
	 */
	public $fieldName;

	/**
	 * @var KalturaGroupUserCreationMode
	 */
	public $creationMode;


	private static $map_between_objects = array(
		'fieldName',
		'creationMode',
	);

	private static $map_dynamic_enum = array();

	private static $map_field_enum = array(
		KalturaEsearchGroupUserFieldName::GROUP_IDS => ESearchGroupUserFieldName::GROUP_IDS,

	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	protected function getItemFieldName()
	{
		return $this->fieldName;
	}

	protected function getDynamicEnumMap()
	{
		return self::$map_dynamic_enum;
	}

	protected function getFieldEnumMap()
	{
		return self::$map_field_enum;
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchGroupUserItem();

		if (in_array($this->fieldName, array(KalturaEsearchGroupUserFieldName::GROUP_IDS)))
		{
			$kuserId = self::KUSER_ID_THAT_DOESNT_EXIST;
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->searchTerm, true);
			if ($kuser)
			{
				$kuserId = $kuser->getId();
			}

			$this->searchTerm = $kuserId;
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

}