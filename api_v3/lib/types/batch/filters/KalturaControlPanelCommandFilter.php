<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaControlPanelCommandFilter extends KalturaControlPanelCommandBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ControlPanelCommandFilter();
	}
}
