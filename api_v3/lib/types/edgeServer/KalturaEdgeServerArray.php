<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEdgeServerArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaEdgeServerArray();
		foreach($arr as $obj)
		{
		    /* @var $obj StorageProfile */
			$nObj = new KalturaEdgeServer();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaEdgeServer" );
	}
}


