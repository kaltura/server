<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCategoryEntryItemFactory
{

	public static function getCoreItemByFieldName($fieldName)
	{
		switch ($fieldName)
		{
			case KalturaESearchCategoryEntryFieldName::ID:
				return new ESearchCategoryEntryIdItem();
			case KalturaESearchCategoryEntryFieldName::NAME:
				return new ESearchCategoryEntryNameItem();
			case KalturaESearchCategoryEntryFieldName::FULL_IDS:
				return new ESearchCategoryEntryFullIdsItem();
			case KalturaESearchCategoryEntryFieldName::ANCESTOR_ID:
				return new ESearchCategoryEntryAncestorIdItem();
			case KalturaESearchCategoryEntryFieldName::ANCESTOR_NAME:
				return new ESearchCategoryEntryAncestorNameItem();
			default:
				KalturaLog::err("Unknown field name $fieldName in ESearchCategoryEntryItemFactory");
				return null;
		}
	}

}
