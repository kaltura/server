<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectFilterEngine
 */
class KObjectFilterBaseEntryEngine extends KObjectFilterServiceEngine
{
	/**
	 * @return string
	 */
	function getServiceId()
	{
		return strtolower($this->getServiceName());
	}

	/**
	 * @return string
	 */
	function getServiceName()
	{
		return 'baseEntry';
	}

	/**
	 * @return string
	 */
	function getActionName()
	{
		return 'list';
	}

	/**
	 * @return BaseEntryService
	 */
	function getServiceInstance()
	{
		return new BaseEntryService();
	}
}