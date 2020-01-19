<?php
/**
 * @package Scheduler
 * @subpackage StorageUpdate
 */

class KAsyncStorageUpdateUtils
{

const PARTNER_STATUS_DELETED = 0;
const PARTNER_STATUS_CONTENT_BLOCK = 2;
const PARTNER_PACKAGE_FREE = 1;
const PARTNER_PACKAGE_INTERNAL_TRIAL = 103;
const PARTNER_PACKAGE_DEVELOPER_TRIAL = 100;
const DAY = 86400; // in seconds
const BLOCKING_DAYS_GRACE = 7;
const KALTURA_ACCOUNT_UPGRADES_NOTIFICATION_EMAIL = 'upgrade@kaltura.com';
const MAIL_PRIORITY_NORMAL = 2;
const REPORT_TYPE_PARTNER_USAGE_DASHBOARD = 202;
const IS_FREE_PACKAGE_PLACE_HOLDER = "{IS_FREE_PACKAGE}";
const DEKIWIKI = 103;

	public static function isPartnerCreatedAsMonitoredFreeTrial($partner)
	{
		if ($partner->partnerPackage == self::PARTNER_PACKAGE_INTERNAL_TRIAL)
		{
			return true;
		}
		if ($partner->partnerPackage == self::PARTNER_PACKAGE_DEVELOPER_TRIAL)
		{
			$freeTrialStartDate = KBatchBase::getConfigParam('new_developer_free_trial_start_date');
		}
		else
		{
			$freeTrialStartDate = KBatchBase::getConfigParam('new_free_trial_start_date');
		}
		if(!$freeTrialStartDate)
		{
			return false;
		}
		$createTime = $partner->createdAt;
		if($createTime >= $freeTrialStartDate)
		{
			return true;
		}
		return false;
	}

	public static function todayOffset ( $delta_in_days )
	{
		$calculated_day = self::DAY * $delta_in_days + time();
		return date ( "Y-m-d" , $calculated_day  );
	}

	public static function diffInDays ($date1, $date2)
	{
		$date1 = new DateTime($date1);
		$date2 = new DateTime($date2);
		$diff = $date2->diff($date1)->format("%a");
		return $diff;
	}

	public static function today()
	{
		return date ( "Y-m-d" , time() );
	}

	/**
	 * retrieve the closest (lowest) notification day compering today
	 *
	 * @param int $search
	 * @param array $arr
	 * @return int
	 */
	public static function getClosestDay($search, $arr) {
		$closest = 0;
		foreach ($arr as $item)
		{
			if (($item <= $search) && (abs($search - $closest) > abs($item - $search)))
				$closest = $item;
		}
		return $closest;
	}

	public static function getEmailLinkHash($partner_id, $partner_secret)
	{
		return md5($partner_secret.$partner_id.KBatchBase::getConfigParam('kaltura_email_hash', 'local', false));
	}

	public static function flatXml2arr($flatXml)
	{
		$arr = array();
		$children = $flatXml->children();
		foreach ($children as $child => $value) {
			if (!$value->children())
			{
				$arr[$child] = "$value";
			}
			else
			{
				$arr[$child] = KAsyncStorageUpdateUtils::flatXml2arr($value);
			}
		}
		return $arr;
	}
}