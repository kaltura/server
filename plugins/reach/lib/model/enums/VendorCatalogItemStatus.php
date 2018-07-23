<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorCatalogItemStatus extends BaseEnum
{
	const DEPRECATED = 1;
	const ACTIVE 	 = 2;
	const DELETED 	 = 3;
}