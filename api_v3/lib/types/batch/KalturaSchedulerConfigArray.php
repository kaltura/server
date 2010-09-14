<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSchedulerConfigArray extends KalturaTypedArray
{
	public static function fromSchedulerConfigArray( $arr )
	{
		$newArr = new KalturaSchedulerConfigArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaSchedulerConfig();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaSchedulerConfig" );
	}
}
?>