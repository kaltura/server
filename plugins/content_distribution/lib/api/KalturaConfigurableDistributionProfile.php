<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 * @abstract
 */
class KalturaConfigurableDistributionProfile extends KalturaDistributionProfile
{

	/**
	 * @var KalturaDistributionFieldConfigArray
	 */
	public $fieldConfigArray;
	
	/**
	 * @var KalturaStringArray
	 */
	public $itemXpathsToExtend;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	 (
		//'fieldConfigArray',
	 );
	 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	
    public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			return null;
			
		parent::toObject($dbObject, $skip);
		
		$dbFieldConfigArray = array();
		if (!is_null($this->fieldConfigArray))
		{
		    foreach ($this->fieldConfigArray as $fieldConfig)
		    {
		        $dbFieldConfigArray[] = $fieldConfig->toObject();
		    }
		}
		$dbObject->setFieldConfigArray($dbFieldConfigArray);
		
		$itemXpathsToExtendArray = array();
		foreach($this->itemXpathsToExtend as $stringObj)
			$itemXpathsToExtendArray[] = $stringObj->value;
			
		$dbObject->setItemXpathsToExtend($itemXpathsToExtendArray);
					
		return $dbObject;
	}
	
	public function fromObject ($source_object)
	{
		parent::fromObject($source_object);
		
		$this->fieldConfigArray = KalturaDistributionFieldConfigArray::fromDbArray($source_object->getFieldConfigArray());
		$this->itemXpathsToExtend = KalturaStringArray::fromStringArray($source_object->getItemXpathsToExtend());
	}
	
	
	
		 
}