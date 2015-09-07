<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaServerNodeFilter extends KalturaServerNodeBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ServerNodeFilter();
	}
}
