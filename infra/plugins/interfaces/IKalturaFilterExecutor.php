<?php
/**
 * Enable plugin to execute KalturaRelatedFilter
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaFilterExecutor extends IKalturaBase
{
	public static function canExecuteFilter(KalturaRelatedFilter $filter, $coreFilter, KalturaDetachedResponseProfile $responseProfile = null);
	public static function executeFilter(KalturaRelatedFilter $filter, $coreFilter, KalturaFilterPager $pager);
}