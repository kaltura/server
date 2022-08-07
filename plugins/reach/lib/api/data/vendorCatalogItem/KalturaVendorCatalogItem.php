<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService VendorCatalogItemService
 */
abstract class KalturaVendorCatalogItem extends KalturaObject implements IRelatedFilterable, IApiObjectFactory
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,notin,order
	 */
	public $id;

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
	 * @var KalturaVendorServiceType
	 * @filter eq,in
	 */
	public $serviceType;

	/**
	 * @var KalturaVendorServiceFeature
	 * @readonly
	 * @filter eq,in
	 */
	public $serviceFeature;

	/**
	 * @var KalturaVendorServiceTurnAroundTime
	 * @filter eq,in
	 */
	public $turnAroundTime;

	/**
	 * @var KalturaVendorCatalogItemPricing
	 * @requiresPermission read
	 */
	public $pricing;

	/**
	 * Property showing the catalog item's engine type, in case a vendor can offer the same service via different engines.
	 * @var KalturaReachVendorEngineType
	 */
	public $engineType;

	/**
	 * @var KalturaCatalogItemLanguage
	 * @filter eq,in
	 */
	public $sourceLanguage;

	/**
	 * @var bool
	 */
	public $allowResubmission = false;

	private static $map_between_objects = array
	(
		'id',
		'vendorPartnerId',
		'name',
		'systemName',
		'createdAt',
		'updatedAt',
		'status',
		'serviceType',
		'serviceFeature',
		'turnAroundTime',
		'pricing',
		'engineType',
		'allowResubmission',
		'sourceLanguage',
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
		$this->validate();
		return parent::validateForInsert($propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validate($sourceObject);
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	private function validate(VendorCatalogItem $sourceObject = null)
	{
		if (!$sourceObject) //Source object will be null on insert
			$this->validatePropertyNotNull(array("vendorPartnerId", "serviceType", "turnAroundTime", "pricing"));

		$this->validateVendorPartnerId($sourceObject);
		$this->validateSystemName($sourceObject);
		$this->validateResubmission($sourceObject);
	}

	public function getExtraFilters()
	{
		return array();
	}

	public function getFilterDocs()
	{
		return array();
	}

	private function validateVendorPartnerId(VendorCatalogItem $sourceObject = null)
	{
		// In case this is update and vendor partner id was not sent don't run validation
		if ($sourceObject && !$this->isNull('vendorPartnerId') && $sourceObject->getVendorPartnerId() == $this->vendorPartnerId)
			return;

		$vendorPartner = PartnerPeer::retrieveByPK($this->vendorPartnerId);
		if (!$vendorPartner)
			throw new KalturaAPIException(KalturaReachErrors::VENDOR_PARTNER_ID_NOT_FOUND, $this->vendorPartnerId);

		if (!PermissionPeer::isValidForPartner(PermissionName::REACH_VENDOR_PARTNER_PERMISSION, $this->vendorPartnerId))
			throw new KalturaAPIException(KalturaReachErrors::PARTNER_NOT_VENDOR, $this->vendorPartnerId);
	}

	private function validateSystemName(VendorCatalogItem $sourceObject = null)
	{
		$this->validatePropertyMinLength('systemName', 3, true);

		$id = $sourceObject ? $sourceObject->getId() : null;
		if (trim($this->systemName) && !$this->isNull('systemName'))
		{
			$systemNameTemplates = VendorCatalogItemPeer::retrieveBySystemName($this->systemName, $id);
			if (count($systemNameTemplates))
				throw new KalturaAPIException(KalturaReachErrors::VENDOR_CATALOG_ITEM_DUPLICATE_SYSTEM_NAME, $this->systemName);
		}
	}

	protected function validateResubmission(VendorCatalogItem $sourceObject = null)
	{
		if (($this->serviceType == KalturaVendorServiceType::HUMAN && $this->allowResubmission) ||
			($sourceObject && $sourceObject->getServiceType() == KalturaVendorServiceType::HUMAN && $this->allowResubmission))
		{
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_ONLY_MACHINE_ALLOWED_RESUBMISSION);
		}
	}

	/* (non-PHPdoc)
 	 * @see IApiObjectFactory::getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile)
 	 */
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$object = null;
		switch ($sourceObject->getServiceFeature())
		{
			case VendorServiceFeature::CAPTIONS:
				$object = new KalturaVendorCaptionsCatalogItem();
				break;

			case VendorServiceFeature::TRANSLATION:
				$object = new KalturaVendorTranslationCatalogItem();
				break;
				
			case VendorServiceFeature::ALIGNMENT:
				$object = new KalturaVendorAlignmentCatalogItem();
				break;
			
			case VendorServiceFeature::AUDIO_DESCRIPTION:
				$object = new KalturaVendorAudioDescriptionCatalogItem();
				break;
			
			case VendorServiceFeature::CHAPTERING:
				$object = new KalturaVendorChapteringCatalogItem();
				break;
			
			case VendorServiceFeature::DUBBING:
				$object = new KalturaVendorDubbingCatalogItem();
				break;

			case VendorServiceFeature::INTELLIGENT_TAGGING:
				$object = new KalturaVendorIntelligentTaggingCatalogItem();
				break;

			case VendorServiceFeature::LIVE_CAPTION:
				$object = new KalturaVendorLiveCaptionCatalogItem();
				break;

			default:
				$object = new KalturaVendorCaptionsCatalogItem();
				break;
		}

		if (!$object)
			return null;

		/* @var $object KalturaVendorCatalogItem */
		$object->fromObject($sourceObject, $responseProfile);
		return $object;
	}
}
