<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.filters
 */
class KalturaBusinessProcessServerFilter extends KalturaBusinessProcessServerBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new BusinessProcessServerFilter();
	}
}
