<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
class KalturaDataCenterContentResource extends KalturaContentResource 
{
	public function getDc()
	{
		return kDataCenterMgr::getCurrentDcId();
	}
}