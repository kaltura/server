<?php
/**
 * @package infra
 * @subpackage utils
 */
class kTime
{
	const REMOVE_DATE = -1;

	public static function getRelativeTime($value)
	{
		// empty fields should be treated as 0 and not as the current time
		if (strlen($value) == 0)
			return 0;
		$value = (int)$value;
		if ($value == self::REMOVE_DATE)
			return $value;
		$maxRelativeTime = kConf::get('max_relative_time');
		if (-$maxRelativeTime <= $value && $value <= $maxRelativeTime && self::isRelativeTimeEnabled())
		{
			$time = self::getTime();
			$value = $time + $value;
		}

		return $value;
	}

	/**
	 * Looks for the time that is stored under ks privilege as reference time.
	 * If not found, returns time().
	 *
	 * @param bool $notifyApiCache
	 * @return int
	 */
	public static function getTime($notifyApiCache = true)
	{
		if (kCurrentContext::$ks_object)
		{
			$referenceTime = kCurrentContext::$ks_object->getPrivilegeValue(ks::PRIVILEGE_REFERENCE_TIME);
			if ($referenceTime)
				return (int)$referenceTime;
		}
		if ($notifyApiCache)
			return kApiCache::getTime();
		else
			return time();
	}

	public static function isRelativeTimeEnabled()
	{
		if (!kConf::hasParam('disable_relative_time_partners'))
			return true;

		return !in_array(kCurrentContext::getCurrentPartnerId(), kConf::get('disable_relative_time_partners'));
	}
}