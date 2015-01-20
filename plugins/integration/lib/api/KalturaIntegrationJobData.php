<?php
/**
 * @package plugins.integration
 * @subpackage api.objects
 */
class KalturaIntegrationJobData extends KalturaJobData
{
	/**
	 * @var KalturaIntegrationProviderType
	 */
	public $providerType;

	/**
	 * Additional data that relevant for the provider only
	 * @var KalturaIntegrationJobProviderData
	 */
	public $providerData;

	/**
	 * @var KalturaIntegrationTriggerType
	 */
	public $triggerType;

	/**
	 * Additional data that relevant for the trigger only
	 * @var KalturaIntegrationJobTriggerData
	 */
	public $triggerData;
	
	private static $map_between_objects = array
	(
		"providerType" ,
		"triggerType" ,
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($srcObj)
	 */
	public function fromObject($sourceObject)
	{
		parent::fromObject($sourceObject);
		
		$providerType = $sourceObject->getProviderType();
		$this->providerData = KalturaPluginManager::loadObject('KalturaIntegrationJobProviderData', $providerType);
		$providerData = $sourceObject->getProviderData();
		if($this->providerData && $providerData && $providerData instanceof kIntegrationJobProviderData)
			$this->providerData->fromObject($providerData);
			
		$triggerType = $sourceObject->getTriggerType();
		$this->triggerData = KalturaPluginManager::loadObject('KalturaIntegrationJobTriggerData', $triggerType);
		$triggerData = $sourceObject->getTriggerData();
		if($this->triggerData && $triggerData && $triggerData instanceof kIntegrationJobTriggerData)
			$this->triggerData->fromObject($triggerData);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
		{
			$object = new kIntegrationJobData();
		} 
		$object = parent::toObject($object, $skip);
				
		if($this->providerType && $this->providerData && $this->providerData instanceof KalturaIntegrationJobProviderData)
		{
			$providerData = KalturaPluginManager::loadObject('kIntegrationJobProviderData', $this->providerType);
			if($providerData)
			{
				$providerData = $this->providerData->toObject($providerData);
				$object->setProviderData($providerData);
			}
		}
		
		if($this->triggerType && $this->triggerData && $this->triggerData instanceof KalturaIntegrationJobTriggerData)
		{
			$triggerData = KalturaPluginManager::loadObject('kIntegrationJobTriggerData', $this->triggerType);
			if($triggerData)
			{
				$triggerData = $this->triggerData->toObject($triggerData);
				$object->setTriggerData($triggerData);
			}
		}
		
		return $object;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		
		$this->validatePropertyNotNull('providerType');
		$this->validatePropertyNotNull('providerData');
		$this->validatePropertyNotNull('triggerType');
		$this->validatePropertyNotNull('triggerData');
	}
	
	/* (non-PHPdoc)
	 * @see KalturaJobData::toSubType()
	 */
	public function toSubType($subType)
	{
		return kPluginableEnumsManager::apiToCore('IntegrationProviderType', $subType);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaJobData::fromSubType()
	 */
	public function fromSubType($subType)
	{
		return kPluginableEnumsManager::coreToApi('IntegrationProviderType', $subType);
	}
}
