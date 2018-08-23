<?php
/**
 * @package plugins.searchHistory
 * @subpackage lib
 */
class searchHistoryUtils
{

	const DEFAULT_SEARCH_CONTEXT = 'DEFAULT_SC';

	public static function getSearchContext()
	{
		$searchContext = null;
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		if ($ks)
		{
			$searchContext = $ks->getSearchContext();
		}
		return $searchContext ? $searchContext : self::DEFAULT_SEARCH_CONTEXT;
	}

	public static function formatMonthPartnerIdUserIdContext($month, $partnerId, $userId, $context)
	{
		return sprintf("m%sp%su%sc%s", $month, $partnerId, $userId, $context);
	}

	public static function formatMonthPartnerIdUserIdContextObject($month ,$partnerId, $userId, $context, $object)
	{
		return sprintf("m%sp%su%sc%so%s", $month, $partnerId, $userId, $context, $object);
	}

	public static function getWeightFromTimestamp($timestamp)
	{
		$month = self::getMonthFromTimestamp($timestamp);
		$firstDayOfTimestampMonth = new DateTime("first day of $month midnight");
		$firstDayOfTimestampMonthEpoc = $firstDayOfTimestampMonth->format("U");
		return $timestamp - $firstDayOfTimestampMonthEpoc;
	}

	public static function getMonthFromTimestamp($timestamp)
	{
		return date("F", $timestamp);
	}

	public static function getBoostMap()
	{
		$now = new DateTime();
		$thisMonth = $now->format("F");
		$MonthBefore = $now->sub(new DateInterval("P1M"));
		$prevMonth = $MonthBefore->format("F");
		$prevPrevMonth = $MonthBefore->sub(new DateInterval("P1M"));
		$prevPrevMonth = $prevPrevMonth->format("F");
		
		return array($thisMonth => 3, $prevMonth => 2, $prevPrevMonth => 1);
	}

}
