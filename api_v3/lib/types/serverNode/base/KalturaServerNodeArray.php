<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaServerNodeArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaServerNodeArray();
		foreach($arr as $obj)
		{
			$nObj = KalturaServerNode::getInstance($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaServerNode" );
	}
}