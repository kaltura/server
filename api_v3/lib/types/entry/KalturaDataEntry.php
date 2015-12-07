<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDataEntry extends KalturaBaseEntry
{
	/**
	 * The data of the entry
	 *
	 * @var string
	 */
	public $dataContent;
	
	/**
	 * indicator whether to return the object for get action with the dataContent field.
	 * @insertonly
	 * @var bool
	 */
	public $retrieveDataContentByGet;
	
	private static $map_between_objects = array(
		"dataContent",
		"retrieveDataContentByGet",
	);
	
	public function __construct()
	{
		$this->type = KalturaEntryType::DATA;
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	

	public function toObject($dbDataEntry = null, $propsToSkip = array())
	{
		if(is_null($dbDataEntry))
			$dbDataEntry = new entry();
			
		if ($this->retrieveDataContentByGet === null)
			$this->retrieveDataContentByGet = 1;
			
		//$dbDataEntry->putInCustomData('retrieveDataContentByGet',$this->retrieveDataContentByGet);
		$dbDataEntry->setRetrieveDataContentByGet($this->retrieveDataContentByGet);
		
		return parent::toObject($dbDataEntry, $propsToSkip);
	}
	
	public function doFromObject($dbDataEntry, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($dbDataEntry, $responseProfile);
		//$retrieveDataContentByGet = $dbDataEntry->getFromCustomData('retrieveDataContentByGet');
		
		$retrieveDataContentByGet = $dbDataEntry->getRetrieveDataContentByGet();
		if($this->shouldGet('retrieveDataContentByGet', $responseProfile))
			$this->retrieveDataContentByGet = $retrieveDataContentByGet;
		
		if($retrieveDataContentByGet != true && $this->shouldGet('dataContent', $responseProfile))
			$this->dataContent = '';
	}
}
