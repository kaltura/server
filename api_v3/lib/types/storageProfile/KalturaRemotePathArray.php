<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRemotePathArray extends KalturaTypedArray
{
	public static function fromFileSyncArray(array $arr)
	{
		$newArr = new KalturaRemotePathArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaRemotePath();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaRemotePath" );
	}
}
