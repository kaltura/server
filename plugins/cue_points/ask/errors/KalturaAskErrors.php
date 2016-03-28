<?php
/**
 * @package plugins.ask
 * @subpackage api.errors
 */

class KalturaAskErrors extends KalturaErrors
{
	const PROVIDED_ENTRY_IS_ALREADY_A_ASK = "PROVIDED_ENTRY_IS_ALREADY_A_ASK;ENTRY_ID; provided entry [@ENTRY_ID@] is already a ask";
	const PROVIDED_ENTRY_IS_NOT_A_ASK = "PROVIDED_ENTRY_IS_NOT_A_ASK;ENTRY_ID; provided entry [@ENTRY_ID@] is not a ask";
	const PARENT_ID_IS_MISSING = "PARENT_ID_IS_MISSING, parent ID is missing";
	const WRONG_PARENT_TYPE = "WRONG_PARENT_TYPE;ENTRY_ID; Parent cue point id [@ENTRY_ID@] has the wrong type";
	const ANSWER_UPDATE_IS_NOT_ALLOWED = "ANSWER_UPDATE_IS_NOT_ALLOWED;ENTRY_ID; Answer update is not allowed for ask [@ENTRY_ID@]";
	const USER_ENTRY_ASK_ALREADY_SUBMITTED = 'USER_ENTRY_ASK_ALREADY_SUBMITTED;The user-entry-ask id is already submitted, answers cannot be added/updated';
	const ENTRY_ID_NOT_GIVEN = 'ENTRY_ID_NOT_GIVEN; No entry id given';
	const NO_SUCH_FILE_TYPE = 'NO_SUCH_FILE_TYPE; Document cannot be provided. No such file type';
	const ASK_CANNOT_BE_DOWNLOAD = 'ASK_CANNOT_BE_DOWNLOAD; Ask cannot be download';
	const ASK_USER_ENTRY_ALREADY_EXISTS = 'ASK_USER_ENTRY_ALREADY_EXISTS;ENTRY_ID;A ask user-entry for the given user-id and entry-id [@ENTRY_ID@] already exists, cannot create duplicate';
}
