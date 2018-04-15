<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCategoryUserItem extends KalturaESearchAbstractCategoryItem
{

	const KUSER_ID_THAT_DOESNT_EXIST = -1;

	/**
	 * @var KalturaESearchCategoryUserFieldName
	 */
	public $fieldName;
	
	/**
	 * @var KalturaCategoryUserPermissionLevel
	 */
	public $permissionLevel;

	/**
	 * @var string
	 */
	public $permissionName;
	
	private static $map_between_objects = array(
		'fieldName',
		'permissionLevel',
		'permissionName',
	);

	private static $map_dynamic_enum = array();

	private static $map_field_enum = array(
		KalturaESearchCategoryUserFieldName::USER_ID => ESearchCategoryUserFieldName::USER_ID,
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
			$object_to_fill = new ESearchCategoryUserItem();

		if(in_array($this->fieldName, array(KalturaESearchCategoryUserFieldName::USER_ID)))
		{
			$kuserId = self::KUSER_ID_THAT_DOESNT_EXIST;
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->searchTerm, true);
			if($kuser)
				$kuserId = $kuser->getId();

			$this->searchTerm = $kuserId;
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

}
