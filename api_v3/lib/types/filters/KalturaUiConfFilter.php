<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUiConfFilter extends KalturaUiConfBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new uiConfFilter();
	}
}
