<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
abstract class KalturaVendorCatalogItem extends KalturaObject implements IRelatedFilterable, IApiObjectFactory
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,order
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;
	
	/**
	 * @var int
	 * @filter eq,in
	 */
	public $vendorPartnerId;
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $systemName;
	
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
	 * @var KalturaNullableBoolean
	 * @filter eq
	 */
	public $isDefault;
	
	/**
	 * @var KalturaVendorServiceType
	 * @filter eq,in
	 */
	public $serviceType;
	
	/**
	 * @var KalturaVendorServiceTurnAroundTime
	 * @filter eq,in
	 */
	public $turnAroundTime;
	
	/**
	 * @var KalturaVendorCatalogItemPricing
	 */
	public $pricing;
	
	
	private static $map_between_objects = array
	(
		'id',
		'partnerId',
		'vendorPartnerId',
		'name',
		'systemName',
		'createdAt',
		'updatedAt',
		'status',
		'isDefault',
		'serviceType',
		'turnAroundTime',
		'pricing',
	);
	
	abstract protected function getServiceFeature();
	
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
			$object_to_fill = new VendorCatalogItem();
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array("vendorPartnerId", "serviceType", "serviceFeature", "turnAroundTime", "pricing"));
		$this->validateVendorPartnerId();
		return parent::validateForInsert($propertiesToSkip);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}

	private function validateVendorPartnerId()
	{
		$vendorPartner = PartnerPeer::retrieveByPK($this->vendorPartnerId);
		
		if(!$vendorPartner)
			throw new KalturaAPIException(KalturaReachErrors::VENDOR_PARTNER_ID_NOT_FOUND, $this->vendorPartnerId);
	
		if($vendorPartner->getType() != KalturaPartnerType::VENDOR)
			throw new KalturaAPIException(KalturaReachErrors::PARTNER_NOT_VENDOR, $this->vendorPartnerId);
	}
	
	/* (non-PHPdoc)
 	 * @see IApiObjectFactory::getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile)
 	 */
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$object = null;
		/* @var $sourceObject VendorCatalogItem */
		switch($sourceObject->getServiceFeature())
		{
			case VendorServiceFeature::CAPTIONS:
				$object = new KalturaVendorCaptionsCatalogItem();
				break;
			
			case VendorServiceFeature::TRANSLATION:
				$object = new KalturaVendorTranslationCatalogItem();
				break;

			default:
				$object = KalturaPluginManager::loadObject('KalturaScheduleResource', $sourceObject->getType());
				if(!$object)
				{
					return null;
				}
		}
		
		$object->fromObject($sourceObject, $responseProfile);
		return $object;
	}
}
