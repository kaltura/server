<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaUiConfAdminArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaUiConfAdminArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaUiConfAdmin();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaUiConfAdmin");
	}
}
