<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaStorageProfileArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaStorageProfileArray();
		foreach($arr as $obj)
		{
		    /* @var $obj StorageProfile */
			$nObj = KalturaStorageProfile::getInstanceByType($obj->getProtocol());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaStorageProfile" );
	}
}
