<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectFilterEngine
 */
class KObjectFilterEntryVendorTaskEngine extends KObjectFilterEngineBase
{
	/**
	 * @param KalturaFilter $filter
	 * @return array
	 */
	public function query(KalturaFilter $filter)
	{
		return $this->_client->entryVendorTask->listAction($filter, $this->getPager());
	}
}