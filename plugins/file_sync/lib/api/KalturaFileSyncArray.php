<?php
/**
 * @package plugins.fileSync
 * @subpackage api.objects
 */
class KalturaFileSyncArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaFileSyncArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaFileSync();
			try
			{
				$nObj->fromObject($obj, $responseProfile);
			}
			catch(kFileSyncException $e)
			{
				continue;
			}
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaFileSync");	
	}
}