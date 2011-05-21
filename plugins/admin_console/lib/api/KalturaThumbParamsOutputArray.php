<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaThumbParamsOutputArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaThumbParamsOutputArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaThumbParamsOutput();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaThumbParamsOutput");	
	}
}