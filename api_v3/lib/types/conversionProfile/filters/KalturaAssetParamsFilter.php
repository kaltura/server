<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaAssetParamsFilter extends KalturaAssetParamsBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new assetParamsFilter();
	}
}
