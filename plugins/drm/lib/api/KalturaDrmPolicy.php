<?php
/**
 * @package plugins.drm
 * @subpackage api.objects
 */
class KalturaDrmPolicy extends KalturaObject implements IFilterable
{	
	/**
	 * @var int
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var int
	 * @insertonly
	 * @filter eq,in
	 */
	public $partnerId;
	
	/**
	 * @var string
	 * @filter like
	 */
	public $name;
	
	/**
	 * @var string
	 * @filter like
	 */
	public $systemName;
	
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var KalturaDrmProviderType
	 * @filter eq,in
	 */
	public $provider;
	
	/**
	 * @var KalturaDrmPolicyStatus
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * @var KalturaDrmLicenseScenario
	 * @filter eq,in
	 */
	public $scenario;
	
	/**
	 * @var KalturaDrmLicenseType
	 */
	public $licenseType;
	
	/**
	 * @var KalturaDrmLicenseExpirationPolicy
	 */
	public $licenseExpirationPolicy;
	
	/**
	 * Duration in days the license is effective
	 * @var int
	 */
	public $duration;
		
	/**
	 * @var int
	 * @readonly
	 */
	public $createdAt;

	/**
	 * @var int
	 * @readonly
	 */
	public $updatedAt;

	/**
	 * @var KalturaKeyValueArray
	 */
	public $licenseParams;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'id',
		'partnerId',
		'name',
		'systemName',
		'description',
		'provider',
		'status',
		'scenario',
		'licenseType',
		'licenseExpirationPolicy',
		'duration',
		'createdAt',
		'updatedAt',
		'licenseParams',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new DrmPolicy();
		parent::toObject($dbObject, $skip);		
		return $dbObject;
	}
		
	/**
	 * @param int $type
	 * @return KalturaDrmPolicy
	 */
	static function getInstanceByType ($provider)
	{
		$obj = KalturaPluginManager::loadObject('KalturaDrmPolicy', $provider);		
		if(!$obj)
			$obj = new KalturaDrmPolicy();
		return $obj;
	}
	
	public function validatePolicy()
	{
	}
	
	public function getExtraFilters()
	{
		return array();
	} 
	
	public function getFilterDocs()
	{
		return null;
	}
	
}