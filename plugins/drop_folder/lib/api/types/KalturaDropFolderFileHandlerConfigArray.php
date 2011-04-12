<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderFileHandlerConfigArray extends KalturaTypedArray
{
	public static function fromDbArray ( $arr )
	{
		$newArr = new KalturaDropFolderFileHandlerConfigArray();
		foreach ( $arr as $obj )
		{
			$nObj = DropFolderPlugin::loadObject('KalturaDropFolderFileHandlerConfig', $obj->getHandlerType());
			if (!$nObj) {
				KalturaLog::err('Cannot instantiate a KalturaDropFolderFileHandlerConfig object of type ['.$obj->getHandlerType().'] - skipping!');
				continue;
			}
			$nObj->fromObject( $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( 'KalturaDropFolderFileHandlerConfig' );
	}
}
