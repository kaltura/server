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
	protected function getAdvancedFilter($clsName)
	{
		if(!KBatchBase::$taskConfig->advancedFilter)
			throw new Exception("Advanced filter undefined");
		
		if(!KBatchBase::$taskConfig->advancedFilter->$clsName)
			throw new Exception("Trying to get undefined advanced-filter for filter of type [$clsName]");
		
		$filter = new $clsName();
		
		foreach (KBatchBase::$taskConfig->advancedFilter->$clsName as $key => $value)
		{
			$filter->$key = $value;
		}
	
		return $filter;
	}
}
