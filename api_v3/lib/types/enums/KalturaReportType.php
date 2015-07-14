<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaReportType extends KalturaDynamicEnum implements ReportType
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'ReportType';
	}

}
