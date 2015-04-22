<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUploadTokenFilter extends KalturaUploadTokenBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new UploadTokenFilter();
	}
}
