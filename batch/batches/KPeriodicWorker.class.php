<?php
/**
 * Base class for all periodic workers.
 * 
 * @package Scheduler
 */
abstract class KPeriodicWorker extends KBatchBase
{
	/**
	 * @return filter by object class name
	 */
	protected function getFilter($clsName)
	{
		if(!KBatchBase::$taskConfig->filter)
			throw new Exception("Filter undefined");
		
		if(!KBatchBase::$taskConfig->filter->$clsName)
			throw new Exception("Trying to get undefined filter for filter of type [$clsName]");
		
		$filter = new $clsName();
		
		foreach (KBatchBase::$taskConfig->filter->$clsName as $key => $value)
		{
			$filter->$key = $value;
		}
	
		return $filter;
	}
}
