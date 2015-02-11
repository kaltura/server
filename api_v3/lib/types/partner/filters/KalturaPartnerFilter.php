<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPartnerFilter extends KalturaPartnerBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new partnerFilter();
	}
}
