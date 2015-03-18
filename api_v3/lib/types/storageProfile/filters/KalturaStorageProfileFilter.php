<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaStorageProfileFilter extends KalturaStorageProfileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new StorageProfileFilter();
	}
}
