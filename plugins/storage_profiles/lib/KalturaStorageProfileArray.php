<?php
/**
 * @package plugins.storageProfile
 * @subpackage api.objects
 */
class KalturaStorageProfileArray extends KalturaTypedArray
{
	public static function fromStorageProfileArray(array $arr)
	{
		$newArr = new KalturaStorageProfileArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaStorageProfile();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaStorageProfile" );
	}
}
?>