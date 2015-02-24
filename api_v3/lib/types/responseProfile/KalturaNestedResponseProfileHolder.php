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
	 */
	public $id;
	
	/**
	 * Unique system name
	 * 
	 * @var string
	 */
	public $systemName;
	
	private static $map_between_objects = array(
		'id', 
		'systemName', 
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
		if($this->isNull('id') && $this->isNull('systemName'))
    		throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('id') . ' and ' . $this->getFormattedPropertyNameWithClassName('systemName'));
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject
	 */
	public function toObject($object = null, $propertiesToSkip = array())
	{
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
		$responseProfile = $this->get();
		return $responseProfile->getRelatedProfiles();
	}

	/* (non-PHPdoc)
	 * @see KalturaNestedResponseProfileBase::get()
	 */
	public function get()
	{
		$responseProfile = null;
		if($this->id)
		{
			$responseProfile = ResponseProfilePeer::retrieveByPK($this->id);
		}
		elseif($this->systemName)
		{
			$responseProfile = ResponseProfilePeer::retrieveBySystemName($this->systemName);
		}
		
		return new KalturaResponseProfile($responseProfile);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaResponseProfileBase::getPager()
	 */
	public function getPager()
	{
		$responseProfile = $this->get();
		return $responseProfile->getPager();
	}
}