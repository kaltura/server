<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRemotePathArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr = null, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaRemotePathArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaRemotePath();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaRemotePath" );
	}
}
