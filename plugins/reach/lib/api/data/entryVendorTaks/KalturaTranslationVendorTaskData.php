<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaTranslationVendorTaskData extends KalturaVendorTaskDataCaptionAsset
{

	/* (non-PHPdoc)
  * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
  */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kTranslationVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}

	/* (non-PHPdoc)
 	 * @see KalturaObject::toInsertableObject()
 	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new kTranslationVendorTaskData();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		if($this->captionAssetId)
		{
			$this->validateCaptionAsset($this->captionAssetId);
		}

		return parent::validateForInsert($propertiesToSkip);
	}

}