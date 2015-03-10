<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaThumbParamsOutputFilter extends KalturaThumbParamsOutputBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new assetParamsOutputFilter();
	}
}
