<?php
/**
 * @package plugins.vendor
 * @subpackage model.enum
 */

interface VendorIntegrationStatus extends BaseEnum
{
	const DISABLED = 1;
	const ACTIVE = 2;
	const DELETED = 3;
}