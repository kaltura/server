<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaControlPanelCommandArray extends KalturaTypedArray
{
	public static function fromControlPanelCommandArray ( $arr )
	{
		$newArr = new KalturaControlPanelCommandArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaControlPanelCommand();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaControlPanelCommand" );
	}
}
?>