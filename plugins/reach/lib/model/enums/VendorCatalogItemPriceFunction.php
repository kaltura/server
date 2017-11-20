<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorCatalogItemPriceFunction extends BaseEnum
{
	const PRICE_PER_SECOND		= "entry::calcPricePerSecond";
	const PRICE_PER_MINUTE		= "entry::calcPricePerMinute";
}