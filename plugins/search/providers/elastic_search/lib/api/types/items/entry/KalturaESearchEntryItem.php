<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchEntryItem extends KalturaESearchAbstractEntryItem
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
		KalturaESearchEntryFieldName::SOURCE_TYPE => 'KalturaSourceType',
		KalturaESearchEntryFieldName::EXTERNAL_SOURCE_TYPE => 'KalturaExternalMediaSourceType'
	);

	private static $map_field_enum = array(
		KalturaESearchEntryFieldName::ID => ESearchEntryFieldName::ID,
		KalturaESearchEntryFieldName::NAME => ESearchEntryFieldName::NAME,
		KalturaESearchEntryFieldName::DESCRIPTION => ESearchEntryFieldName::DESCRIPTION,
		KalturaESearchEntryFieldName::TAGS => ESearchEntryFieldName::TAGS,
		KalturaESearchEntryFieldName::USER_ID => ESearchEntryFieldName::USER_ID,
		KalturaESearchEntryFieldName::CREATOR_ID => ESearchEntryFieldName::CREATOR_ID,
		KalturaESearchEntryFieldName::START_DATE => ESearchEntryFieldName::START_DATE,
		KalturaESearchEntryFieldName::END_DATE => ESearchEntryFieldName::END_DATE,
		KalturaESearchEntryFieldName::REFERENCE_ID => ESearchEntryFieldName::REFERENCE_ID,
		KalturaESearchEntryFieldName::CONVERSION_PROFILE_ID => ESearchEntryFieldName::CONVERSION_PROFILE_ID,
		KalturaESearchEntryFieldName::REDIRECT_ENTRY_ID => ESearchEntryFieldName::REDIRECT_ENTRY_ID,
		KalturaESearchEntryFieldName::ENTITLED_USER_EDIT => ESearchEntryFieldName::ENTITLED_USER_EDIT,
		KalturaESearchEntryFieldName::ENTITLED_USER_PUBLISH => ESearchEntryFieldName::ENTITLED_USER_PUBLISH,
		KalturaESearchEntryFieldName::ENTITLED_USER_VIEW => ESearchEntryFieldName::ENTITLED_USER_VIEW,
		KalturaESearchEntryFieldName::TEMPLATE_ENTRY_ID => ESearchEntryFieldName::TEMPLATE_ENTRY_ID,
		KalturaESearchEntryFieldName::PARENT_ENTRY_ID => ESearchEntryFieldName::PARENT_ENTRY_ID,
		KalturaESearchEntryFieldName::MEDIA_TYPE => ESearchEntryFieldName::MEDIA_TYPE,
		KalturaESearchEntryFieldName::SOURCE_TYPE => ESearchEntryFieldName::SOURCE_TYPE,
		KalturaESearchEntryFieldName::RECORDED_ENTRY_ID => ESearchEntryFieldName::RECORDED_ENTRY_ID,
		KalturaESearchEntryFieldName::PUSH_PUBLISH => ESearchEntryFieldName::PUSH_PUBLISH,
		KalturaESearchEntryFieldName::LENGTH_IN_MSECS => ESearchEntryFieldName::LENGTH_IN_MSECS,
		KalturaESearchEntryFieldName::CREATED_AT => ESearchEntryFieldName::CREATED_AT,
		KalturaESearchEntryFieldName::UPDATED_AT => ESearchEntryFieldName::UPDATED_AT,
		KalturaESearchEntryFieldName::MODERATION_STATUS => ESearchEntryFieldName::MODERATION_STATUS,
		KalturaESearchEntryFieldName::ENTRY_TYPE => ESearchEntryFieldName::ENTRY_TYPE,
		KalturaESearchEntryFieldName::ADMIN_TAGS => ESearchEntryFieldName::ADMIN_TAGS,
		KalturaESearchEntryFieldName::CREDIT => ESearchEntryFieldName::CREDIT,
		KalturaESearchEntryFieldName::SITE_URL => ESearchEntryFieldName::SITE_URL,
		KalturaESearchEntryFieldName::ACCESS_CONTROL_ID => ESearchEntryFieldName::ACCESS_CONTROL_ID,
		KalturaESearchEntryFieldName::EXTERNAL_SOURCE_TYPE => ESearchEntryFieldName::EXTERNAL_SOURCE_TYPE,
		KalturaESearchEntryFieldName::IS_QUIZ => ESearchEntryFieldName::IS_QUIZ,
		KalturaESearchEntryFieldName::IS_LIVE => ESearchEntryFieldName::IS_LIVE,
		KalturaESearchEntryFieldName::USER_NAMES => ESearchEntryFieldName::USER_NAMES,
		KalturaESearchEntryFieldName::ROOT_ID => ESearchEntryFieldName::ROOT_ID,
		KalturaESearchEntryFieldName::PARTNER_SORT_VALUE => ESearchEntryFieldName::PARTNER_SORT_VALUE,
		KalturaESearchEntryFieldName::CAPTIONS_CONTENT => ESearchEntryFieldName::CAPTIONS_CONTENT,
		KalturaESearchEntryFieldName::LAST_PLAYED_AT => ESearchEntryFieldName::LAST_PLAYED_AT,
		KalturaESearchEntryFieldName::PLAYS => ESearchEntryFieldName::PLAYS,
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchEntryItem();

		if(in_array($this->fieldName, array(KalturaESearchEntryFieldName::USER_ID, KalturaESearchEntryFieldName::ENTITLED_USER_EDIT,
			KalturaESearchEntryFieldName::ENTITLED_USER_PUBLISH, KalturaESearchEntryFieldName::ENTITLED_USER_VIEW, KalturaESearchEntryFieldName::CREATOR_ID)))
		{
			$kuserId = self::KUSER_ID_THAT_DOESNT_EXIST;
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->searchTerm, true);
			if($kuser)
				$kuserId = $kuser->getId();

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

	protected function getFieldEnumMap()
	{
		return self::$map_field_enum;
	}
}
