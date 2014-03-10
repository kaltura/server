<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectFilterEngine
 */
class KObjectFilterBaseEntryEngine extends KObjectFilterEngineBase
{
	/**
	 * @param KalturaFilter $filter
	 * @return array
	 */
	public function query(KalturaFilter $filter)
	{
		return $this->_client->baseEntry->listAction($filter, $this->getPager());
	}
}