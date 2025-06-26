<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorSignLanguageCatalogItem extends KalturaVendorCatalogItem
{
	/**
	 * @var KalturaCatalogItemSignLanguage
	 * @filter eq,in
	 */
	public $targetLanguage;

	private static $map_between_objects = array
	(
		'targetLanguage'
	);

    protected function getServiceFeature()
    {
        return KalturaVendorServiceFeature::SIGN_LANGUAGE;
    }

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

    /* (non-PHPdoc)
    * @see KalturaObject::toInsertableObject()
    */
    public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
    {
        if (is_null($object_to_fill))
		{
			$object_to_fill = new VendorSignLanguageCatalogItem();
		}

        return parent::toInsertableObject($object_to_fill, $props_to_skip);
    }

    /* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
    public function toObject($sourceObject = null, $propertiesToSkip = array())
    {
        if(is_null($sourceObject))
        {
            $sourceObject = new VendorSignLanguageCatalogItem();
        }

        return parent::toObject($sourceObject, $propertiesToSkip);
    }

	protected function validate(VendorCatalogItem $sourceObject = null)
	{
		parent::validate($sourceObject);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array("targetLanguage"));
		parent::validateForInsert($propertiesToSkip);
	}
}
