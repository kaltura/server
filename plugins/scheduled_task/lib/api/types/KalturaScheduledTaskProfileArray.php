<?php
/**
 * @package plugins.scheduledTask
 * @subpackage api.objects
 */
class KalturaScheduledTaskProfileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaScheduledTaskProfileArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaScheduledTaskProfile();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaScheduledTaskProfile");
	}
}