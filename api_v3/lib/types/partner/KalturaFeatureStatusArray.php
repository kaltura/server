<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFeatureStatusArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaFeatureStatusArray();
		foreach($arr as $obj)
		{
			if ($obj){
				$nObj = new KalturaFeatureStatus();
				$nObj->fromObject($obj, $responseProfile);
				$newArr[] = $nObj;
			}
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaFeatureStatus" );
	}
}