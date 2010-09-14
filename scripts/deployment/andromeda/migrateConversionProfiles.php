<?php
class migrateConversionProfiles extends AndromedaMigration
{
	/**
	 * @param ConversionProfile $ConversionProfile
	 * @return bool
	 */	
	public static function migrateSingleConversionProfile($ConversionProfile)
	{
		// this action should always succeed
		myConversionProfileUtils::createConversionProfile2FromConversionProfile($ConversionProfile);
		return true;
	}
	
	private static $failedIds;
	
	public static function getFailedIds()
	{
		return self::$failedIds;
	}
	
	/**
	 * migrate a list of conversion-profile IDs. return value is integer: 0 - all failed, 1 - all OK, 2 - some failed
	 * @param array $arrConvProfiles
	 * @return int
	 */
	public static function migrateConversionProfileList($arrConvProfiles)
	{
		if(!count($arrConvProfiles) || !is_array($arrConvProfiles))
			return FALSE;
		self::$failedIds = array();
		foreach($arrConvProfiles as $profile)
		{
			$result = self::migrateSingleConversionProfile($profile);
			if(!$result) self::$failedIds[] = $profile->getId();
		}
		if(count(self::$failedIds) == 0)
			return 1;
		elseif(count(self::$failedIds) == count($arrConvProfiles))
			return 0;
		else
			return 2;
	}
}