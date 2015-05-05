<?php

/**
 * @package api
 * @subpackage filters
 */
class KalturaUserEntryFilter extends KalturaFilter
{
	/**
	 * @return baseObjectFilter
	 */
	protected function getCoreFilter()
	{
		// TODO: Implement getCoreFilter() method.
		return new UserEntryFilter();
	}


}