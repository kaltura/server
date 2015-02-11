<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaNestedResponseProfileHolder extends KalturaNestedResponseProfileBase
{
	/**
	 * Auto generated numeric identifier
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * Unique system name
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	private static $map_between_objects = array(
		'id', 
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array('id', 'systemName'));
	}
	
	protected function setId()
	{
		if(is_null($this->id))
		{
			$responseProfile = ResponseProfilePeer::retrieveBySystemName($this->systemName);
			if(!$responseProfile) 
			{
				throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_NAME_NOT_FOUND, $this->systemName);
			}
			$this->id = $responseProfile->getId();
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject
	 */
	public function toObject($object = null, $propertiesToSkip = array())
	{
		$this->setId();
		
		if(is_null($object))
		{
			$object = new kResponseProfileHolder();
		}
		parent::toObject($object, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaResponseProfileBase::getRelatedProfiles()
	 */
	public function getRelatedProfiles()
	{
		$this->setId();
		$coreResponseProfile = ResponseProfilePeer::retrieveByPK($this->id);
		$responseProfile = new KalturaResponseProfile($coreResponseProfile);
		
		return $responseProfile->getRelatedProfiles();
	}
}