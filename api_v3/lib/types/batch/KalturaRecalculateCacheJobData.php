<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaRecalculateCacheJobData extends KalturaJobData
{
	/**
	 * @param string $subType
	 * @return int
	 */
	public function toSubType($subType)
	{
		return kPluginableEnumsManager::apiToCore('RecalculateCacheType', $subType);
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		return kPluginableEnumsManager::coreToApi('RecalculateCacheType', $subType);
	}
}
