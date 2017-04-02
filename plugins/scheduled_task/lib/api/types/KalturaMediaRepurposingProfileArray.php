<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects
 */
class KalturaMediaRepurposingProfileArray extends KalturaTypedArray
{
	public function __construct()
	{
		parent::__construct('KalturaMediaRepurposingProfile');
	}

	public static function fromDbArray(array $dbArray, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$apiArray = new KalturaMediaRepurposingProfileArray();
		foreach($dbArray as $dbObject)
		{
			$apiObject = new KalturaMediaRepurposingProfile();
			$apiObject->fromObject($dbObject, $responseProfile);;
			$apiArray[] = $apiObject;
		}

		return $apiArray;
	}
}