<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventNotificationParameterArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaEventNotificationParameterArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$parameterType = get_class($obj);
			switch ($parameterType)
			{
				case 'kEventNotificationParameter':
    				$nObj = new KalturaEventNotificationParameter();
					break;
					
				case 'kEventNotificationArrayParameter':
    				$nObj = new KalturaEventNotificationArrayParameter();
					break;
					
				default:
    				$nObj = KalturaPluginManager::loadObject('KalturaEventNotificationParameter', $parameterType);
			}
			
			if($nObj)
			{
				$nObj->fromObject($obj, $responseProfile);
				$newArr[] = $nObj;
			}
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaEventNotificationParameter");	
	}
}