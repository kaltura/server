<?php
/**
 * @package plugins.vendor
 * @subpackage model.enum
 */

interface kZoomErrorMessages extends BaseEnum
{
	const MISSING_ZOOM_CLIENT_CONFIGURATION = 'Missing zoom client configuration';
	const TOKEN_EXPIRED = 'Access token expired';
	const NO_INTEGRATION_DATA = 'Zoom integration data does not exist for current account';
	const USER_NOT_BELONG_TO_ACCOUNT = 'User not belong to this account';
	const PARENT_CATEGORY_NOT_FOUND = 'Could not find parent category id ';
	const UPLOAD_DISABLED = 'Uploads are disabled for current Partner';
	const TOKEN_PARSING_FAILED = 'Parse Tokens failed, response received from zoom is: ';
	const FAILED_VERIFICATION = 'ZOOM - verification token is different from existing token';
	const MISSING_ENTRY_FOR_ZOOM_MEETING = 'Could not find entry for meeting id: ';
	const MISSING_ENTRY_FOR_CHAT = 'Missing entry for the chat file';
	const ERROR_HANDLING_CHAT = 'Error while trying to handle chat file';
	const ERROR_HANDLING_TRANSCRIPT = 'Error while trying to handle transcript file';
}