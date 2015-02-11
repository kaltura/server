<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaDropFolderArray();
		foreach ( $arr as $obj )
		{
		    $nObj = KalturaDropFolder::getInstanceByType($obj->getType());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( 'KalturaDropFolder' );
	}
}
