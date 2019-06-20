<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.errors
 */

class kVendorException extends kCoreException
{
	const TOKEN_EXPIRED = 'access token expired';
	const NO_INTEGRATION_DATA = 'Zoom integration data does not exist for current account';
}