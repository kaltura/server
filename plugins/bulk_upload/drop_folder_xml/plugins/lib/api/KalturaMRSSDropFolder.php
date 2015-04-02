<?php
/**
 * @package plugins.dropFolderMRSS
 * @subpackage api.objects
 */
class KalturaMRSSDropFolder extends KalturaDropFolder
{
	/**
	 * URL of the MRSS indicating the entries to be uploaded
	 * @var string
	 */
	public $mrssUrl;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'mrssUrl',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new MRSSDropFolder();
		$this->validate();
		$dbObject->setType(DropFolderMRSSPlugin::getDropFolderTypeCoreValue(MRSSDropFolderType::MRSS));
		return parent::toObject($dbObject, $skip);
	}
}