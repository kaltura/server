<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.filters
 */
class KalturaDropFolderFileFilter extends KalturaDropFolderFileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new DropFolderFileFilter();
	}
}
