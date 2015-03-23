<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaDeliveryProfileFilter extends KalturaDeliveryProfileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new DeliveryProfileFilter();
	}
}
