<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorDocumentEnrichmentCatalogItem extends KalturaVendorCatalogItem
{
	/**
	 * @var KalturaVendorDocumentEnrichmentType
	 */
	public $documentEnrichmentType;

	private static $map_between_objects = array
	(
		'documentEnrichmentType',
	);

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	protected function getServiceFeature(): int
	{
		return VendorServiceFeature::DOCUMENT_ENRICHMENT;
	}

	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new VendorDocumentEnrichmentCatalogItem();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
		{
			$object_to_fill = new VendorDocumentEnrichmentCatalogItem();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
