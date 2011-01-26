<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaTrackEntryArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaTrackEntryArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaTrackEntry();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaTrackEntry");	
	}
}