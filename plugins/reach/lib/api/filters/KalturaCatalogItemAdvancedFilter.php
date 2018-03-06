<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaCatalogItemAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var int
	 */
	public $idEqual;
	
	/**
	 * @var string
	 */
	public $idIn;
	
	/**
	 * @var string
	 */
	public $idNotIn;
	
	/**
	 * @var int
	 */
	public $vendorPartnerIdEqual;
	
	/**
	 * @var string
	 */
	public $vendorPartnerIdIn;
	
	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;
	
	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;
	
	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;
	
	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;
	
	/**
	 * @var KalturaVendorCatalogItemStatus
	 */
	public $statusEqual;
	
	/**
	 * @var string
	 */
	public $statusIn;
	
	/**
	 * @var KalturaVendorServiceType
	 */
	public $serviceTypeEqual;
	
	/**
	 * @var string
	 */
	public $serviceTypeIn;
	
	/**
	 * @var KalturaVendorServiceFeature
	 */
	public $serviceFeatureEqual;
	
	/**
	 * @var string
	 */
	public $serviceFeatureIn;
	
	/**
	 * @var KalturaVendorServiceTurnAroundTime
	 */
	public $turnAroundTimeEqual;
	
	/**
	 * @var string
	 */
	public $turnAroundTimeIn;
	
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kCatalogItemAdvancedFilter();
		
		$baseFilter = $this->buildBaseFilter();
		$object_to_fill->setBaseFilter($baseFilter);
		return parent::toObject($object_to_fill, $props_to_skip);
	}
	
	public function buildBaseFilter ()
	{
		$catalogItemFilter = new KalturaVendorCatalogItemFilter();
		foreach ($this as $key=>$value)
		{
			$catalogItemFilter->$key = $value;
		}
		
		return $catalogItemFilter->toObject();
	}
}