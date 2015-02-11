<?php
/**
 * @package plugins.emailNotification
 * @subpackage api.objects
 */
class KalturaEmailNotificationRecipientArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaEmailNotificationRecipientArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaEmailNotificationRecipient();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaEmailNotificationRecipient");	
	}
}