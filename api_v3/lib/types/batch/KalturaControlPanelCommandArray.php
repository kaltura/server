<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaControlPanelCommandArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaControlPanelCommandArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaControlPanelCommand();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaControlPanelCommand" );
	}
}
