<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService PartnerCatalogItemService
 */
class KalturaPartnerCatalogItem extends KalturaObject implements IRelatedFilterable, IApiObjectFactory
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,notin,order
	 */
	public $id;

	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;

	/**
	 * @var KalturaVendorCatalogItemStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;

	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;

	/**
	 * @var int
	 * @readonly
	 */
	public $catalogItemId;

	private static $map_between_objects = array
	(
		'id',
		'createdAt',
		'updatedAt',
		'status',
		'partnerId',
		'catalogItemId',
	);

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
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
			$object_to_fill = new PartnerCatalogItem();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validate();
		return parent::validateForInsert($propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validate($sourceObject);
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	private function validate(PartnerCatalogItem $sourceObject = null)
	{
		if (!$sourceObject) //Source object will be null on insert
		{
			$this->validatePropertyNotNull(array("partnerId", "catalogItemId"));
		}

		$partner = PartnerPeer::retrieveByPK($this->partnerId);
		if (!$partner)
		{
			// Edit error - partner doesnt exist
			throw new KalturaAPIException(KalturaReachErrors::VENDOR_PARTNER_ID_NOT_FOUND, $this->partnerId);
		}

		$catalogItem = VendorCatalogItemPeer::retrieveByPK($this->catalogItemId);
		if (!$catalogItem)
		{
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $this->catalogItemId);
		}
	}

	public function getExtraFilters()
	{
		return array();
	}

	public function getFilterDocs()
	{
		return array();
	}

	/* (non-PHPdoc)
 	 * @see IApiObjectFactory::getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile)
 	 */
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$object = new KalturaPartnerCatalogItem();
		$object->fromObject($sourceObject, $responseProfile);
		return $object;
	}
}
