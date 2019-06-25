<?php
/**
 * @package plugins.vendor
 * @subpackage model.enum
 */

interface kVendorErrorMessages extends BaseEnum
{
	const TOKEN_EXPIRED = 'access token expired';
	const NO_INTEGRATION_DATA = 'Zoom integration data does not exist for current account';
	const USER_NOT_BELONG_TO_ACCOUNT = 'User not belong to this account';
	const PARENT_CATEGORY_NOT_FOUND = 'Could not find parent category id ';
	const UPLOAD_DISABLED = 'Uploads are disabled for current Partner';
	const TOKEN_PARSING_FAILED = 'Parse Tokens failed, response received from zoom is: ';
}