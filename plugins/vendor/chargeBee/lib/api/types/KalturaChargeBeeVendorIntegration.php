<?php
/**
 * @package plugins.chargeBee
 * @subpackage api.objects
 */
class KalturaChargeBeeVendorIntegration extends KalturaObject implements IFilterable
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;

	/**
	 * @var time
	 */
	public $lastAccessToChargeBee;

	/**
	 * @var string
	 */
	public $handledEventIds;

	/**
	 * @var KalturaVendorTypeEnum
	 * @filter eq,in
	 */
	public $type;

	/**
	 * @var string
	 * @filter eq
	 */
	public $subscriptionId;

	/**
	 * @var KalturaVendorStatus
	 * @filter eq,in
	 */
	public $status;

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
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $partnerId;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'id',
		'lastAccessToChargeBee',
		'handledEventIds',
		'type' => 'vendorType',
		'subscriptionId' => 'accountId',
		'status',
		'createdAt',
		'updatedAt',
		'partnerId',
	);

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
			$object_to_fill = new kChargeBeeVendorIntegration();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function getExtraFilters()
	{
		return array();
	}

	public function getFilterDocs()
	{
		return array();
	}
}