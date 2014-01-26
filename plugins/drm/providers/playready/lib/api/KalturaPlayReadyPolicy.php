<?php
/**
 * @package plugins.drm
 * @subpackage api.objects
 */
class KalturaPlayReadyPolicy extends KalturaDrmPolicy
{
    /**
	 * @var int
	 */
	public $gracePeriod;	
	
	/**
	 * @var KalturaPlayReadyLicenseRemovalPolicy
	 */
	public $licenseRemovalPolicy;	
	
	/**
	 * @var int
	 */
	public $licenseRemovalDuration;	
	
	/**
	 * @var KalturaPlayReadyMinimumLicenseSecurityLevel
	 */
	public $minSecurityLevel;	
	
	/**
	 * @var KalturaPlayReadyRightArray
	 */
	public $rights;	
	
	
	private static $map_between_objects = array(
		'gracePeriod',
		'licenseRemovalPolicy',
		'licenseRemovalDuration',
		'minSecurityLevel',
		'rights',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new PlayReadyPolicy();
			
		parent::toObject($dbObject, $skip);
					
		return $dbObject;
	}
	
	public function validatePolicy()
	{
		if(count($this->rights))
		{
			foreach ($this->rights as $right) 
			{
				if($right instanceof KalturaPlayReadyPlayRight)
				{
					$this->validatePlayRight($right);
				}
				else if($right instanceof KalturaPlayReadyCopyRight)
				{
					$this->validateCopyRight($right);
				}
			}
		}
		
		parent::validatePolicy();
	}
	
	private function validatePlayRight(KalturaPlayReadyPlayRight $right)
	{
		if(	count($right->analogVideoOutputProtectionList) && 
			in_array(KalturaPlayReadyAnalogVideoOPId::EXPLICIT_ANALOG_TV, $right->analogVideoOutputProtectionList) && 
			in_array(KalturaPlayReadyAnalogVideoOPId::BEST_EFFORT_EXPLICIT_ANALOG_TV, $right->analogVideoOutputProtectionList))
		{
			throw new KalturaAPIException(KalturaPlayReadyErrors::ANALOG_OUTPUT_PROTECTION_ID_NOT_ALLOWED, KalturaPlayReadyAnalogVideoOPId::EXPLICIT_ANALOG_TV, KalturaPlayReadyAnalogVideoOPId::BEST_EFFORT_EXPLICIT_ANALOG_TV);
		}
	}
	
	private function validateCopyRight(KalturaPlayReadyCopyRight $right)
	{
		if($right->copyCount > 0 && !count($right->copyEnablers))
		{
			throw new KalturaAPIException(KalturaPlayReadyErrors::COPY_ENABLER_TYPE_MISSING);
		}
	}
	
}

