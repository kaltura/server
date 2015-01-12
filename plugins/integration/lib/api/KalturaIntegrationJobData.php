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
	
	private static $map_between_objects = array
	(
		"providerType" ,
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
		
		return $object;
	}
	
	public function validateForUsage($sourceObject)
	{
		$this->validatePropertyNotNull('providerType');
		$this->validatePropertyNotNull('providerData');
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
