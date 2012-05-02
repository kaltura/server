<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFeatureStatusArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr)
	{
		$newArr = new KalturaFeatureStatusArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaFeatureStatus();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaFeatureStatus" );
	}
}