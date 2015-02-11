<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaConversionProfileFilter extends KalturaConversionProfileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ConversionProfileFilter();
	}
}
