<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaReportFilter extends KalturaReportBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ReportFilter();
	}
}
