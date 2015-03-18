<?php
/**
 * @package plugins.audit
 * @subpackage api.objects
 */
class KalturaAuditTrailChangeItemArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaAuditTrailChangeItemArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaAuditTrailChangeItem();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaAuditTrailChangeItem");	
	}
	
	public function toObjectArray()
	{
		$ret = array();
		
		foreach($this as $item)
			$ret[] = $item->toObject();
			
		return $ret;
	}
}
