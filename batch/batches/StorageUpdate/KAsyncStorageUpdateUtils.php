<?php
/**
 * @package Scheduler
 * @subpackage StorageUpdate
 */

class KAsyncStorageUpdateUtils
{

const PAGE_INDEX = 1;
const PAGE_SIZE = 500;
const LOWEST_PARTNER = 100;
const PARTNER_STATUS_DELETED = 0;
const PARTNER_STATUS_CONTENT_BLOCK = 2;
const PARTNER_PACKAGE_FREE = 1;
const PARTNER_PACKAGE_INTERNAL_TRIAL = 103;
const PARTNER_PACKAGE_DEVELOPER_TRIAL = 100;
const DAY = 86400; // in seconds
const BLOCKING_DAYS_GRACE = 7;
const KALTURA_ACCOUNT_UPGRADES_NOTIFICATION_EMAIL = 'upgrade@kaltura.com';
const MAIL_PRIORITY_NORMAL = 2;
const DEKIWIKI = 103;
const LOCAL = 'local';
const KALTURA_EMAIL_HASH = 'kaltura_email_hash';
const NEW_DEVELOPER_FREE_TRIAL_START_DATE = 'new_developer_free_trial_start_date';
const NEW_FREE_TRIAL_START_DATE = 'new_free_trial_start_date';
const PARTNER_NOTIFICATION_EMAIL = 'partner_notification_email';
const PARTNER_NOTIFICATION_NAME = 'partner_notification_name';
const FORMAT_Y_M_D = 'Y-m-d';

	public static function diffInDays ($date1, $date2)
	{
		$date1 = new DateTime($date1);
		$date2 = new DateTime($date2);
		$diff = $date2->diff($date1)->format("%a");
		return $diff;
	}

	public static function today()
	{
		return date ( self::FORMAT_Y_M_D , time() );
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
				$arr[$child] = self::flatXml2arr($value);
			}
		}
		return $arr;
	}
}