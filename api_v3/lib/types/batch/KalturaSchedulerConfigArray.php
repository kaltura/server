<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSchedulerConfigArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaSchedulerConfigArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaSchedulerConfig();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaSchedulerConfig" );
	}
}
