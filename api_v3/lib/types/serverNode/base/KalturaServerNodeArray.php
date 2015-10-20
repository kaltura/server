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
		    /* @var $obj StorageProfile */
			$nObj = KalturaServerNode::getInstance($obj);
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaServerNode" );
	}
}