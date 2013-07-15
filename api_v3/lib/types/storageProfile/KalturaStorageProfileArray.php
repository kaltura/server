<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaStorageProfileArray extends KalturaTypedArray
{
	public static function fromStorageProfileArray(array $arr)
	{
		$newArr = new KalturaStorageProfileArray();
		foreach($arr as $obj)
		{
		    /* @var $obj StorageProfile */
			$nObj = KalturaStorageProfile::getInstanceByType($obj->getProtocol());
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