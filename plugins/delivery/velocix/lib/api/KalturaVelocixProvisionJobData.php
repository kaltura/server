<?php
/**
 * @package plugins.velocix
 * @subpackage lib.api
 */
class KalturaVelocixProvisionJobData extends KalturaProvisionJobData
{
	/**
	 * @var KalturaKeyValueArray
	 */
	public $provisioningParams;
	
	/**
	 * @var string
	 */
	public $userName;
	
	/**
	 * @var string
	 */
	public $password;
	
	
	private static $map_between_objects = array
	(
		"provisioningParams",
		"userName",
		"password",
	);

	/* (non-PHPdoc)
	 * @see KalturaProvisionJobData::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	
	/* (non-PHPdoc)
	 * @see KalturaProvisionJobData::toObject()
	 */
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kVelocixProvisionJobData();
			
		$dbData = parent::toObject($dbData, $props_to_skip);
		
		if (!is_null($this->provisioningParams))
			$dbData->setProvisioningParams($this->toKeyValueArray($this->provisioningParams));
			
		return $dbData;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject ( $source_object  )
	{
		parent::fromObject($source_object);
		$this->provisioningParams = KalturaKeyValueArray::fromKeyValueArray($source_object->getProvisioningParams());
	}
	
 	protected function toKeyValueArray($apiKeyValueArray)
	{
		$keyValueArray = array();
		if (count($apiKeyValueArray))
		{
			foreach($apiKeyValueArray as $keyValueObj)
			{
				/* @var $keyValueObj KalturaKeyValue */
				$keyValueArray[$keyValueObj->key] = $keyValueObj->value;
			}
		}
		return $keyValueArray;
	}
	
}