<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPermissionItemArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaPermissionItemArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			if ($obj->getType() == PermissionItemType::API_ACTION_ITEM) {
				$nObj = new KalturaApiActionPermissionItem();
			}
			else if ($obj->getType() == PermissionItemType::API_PARAMETER_ITEM) {
				$nObj = new KalturaApiParameterPermissionItem();
			}
			else {
				KalturaLog::crit('Unknown permission item type ['.$obj->getType().'] defined with id ['.$obj->getId().'] - skipping!');
				continue;
			}
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct('KalturaPermissionItem');	
	}
}
