<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaIntelligentTaggingVendorTaskData extends KalturaVendorTaskData
{

    /**
     * Optional - The id of the caption asset object
     * @insertonly
     * @var string
     */
    public $assetId;

    /* (non-PHPdoc)
    * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
    */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kIntelligentTaggingVendorTaskData();
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
			$object_to_fill = new kIntelligentTaggingVendorTaskData();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		if($this->assetId)
		{
			$this->validateAsset($this->assetId);
		}

		return parent::validateForInsert($propertiesToSkip);
	}

    protected function validateAsset($assetId)
    {
        $asset = assetPeer::retrieveById($assetId);
        if (!$asset)
        {
            throw new KalturaAPIException(KalturaErrors::ASSET_ID_NOT_FOUND, $assetId);
        }
    }

}