<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaUiConfAdminArray extends KalturaTypedArray
{
	public static function fromUiConfAdminArray($arr)
	{
		$newArr = new KalturaUiConfAdminArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaUiConfAdmin();
			$nObj->fromUiConf( $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaUiConfAdmin");
	}
}
?>