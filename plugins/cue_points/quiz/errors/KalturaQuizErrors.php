<?php
/**
 * @package plugins.quiz
 * @subpackage api.errors
 */

class KalturaQuizErrors extends KalturaErrors
{
	const PROVIDED_ENTRY_IS_ALREADY_A_QUIZ = "PROVIDED_ENTRY_IS_ALREADY_A_QUIZ;ENTRY_ID;Provided entry [@ENTRY_ID@] is already a quiz";
	const PROVIDED_ENTRY_IS_NOT_A_QUIZ = "PROVIDED_ENTRY_IS_NOT_A_QUIZ;ENTRY_ID;Provided entry [@ENTRY_ID@] is not a quiz";
	const PARENT_ID_IS_MISSING = "PARENT_ID_IS_MISSING;;Parent ID is missing";
	const WRONG_PARENT_TYPE = "WRONG_PARENT_TYPE;ENTRY_ID;Parent cue point id [@ENTRY_ID@] has the wrong type";
	const ANSWER_UPDATE_IS_NOT_ALLOWED = "ANSWER_UPDATE_IS_NOT_ALLOWED;ENTRY_ID;Answer update is not allowed for quiz [@ENTRY_ID@]";
	const USER_ENTRY_QUIZ_ALREADY_SUBMITTED = 'USER_ENTRY_QUIZ_ALREADY_SUBMITTED;;The user-entry-quiz id is already submitted, answers cannot be added/updated';
	const ENTRY_ID_NOT_GIVEN = 'ENTRY_ID_NOT_GIVEN;;No entry id given';
	const NO_SUCH_FILE_TYPE = 'NO_SUCH_FILE_TYPE;;Document cannot be provided. No such file type';
	const QUIZ_CANNOT_BE_DOWNLOAD = 'QUIZ_CANNOT_BE_DOWNLOAD;;Quiz cannot be download';
	const QUIZ_USER_ENTRY_ALREADY_EXISTS = 'QUIZ_USER_ENTRY_ALREADY_EXISTS;ENTRY_ID;A quiz user-entry for the given user-id and entry-id [@ENTRY_ID@] already exists, cannot create duplicate';
	const NO_RETAKES_LEFT= 'NO_MORE_RETAKES_ALLOWED;ENTRY_ID;No Retakes left for the given user-id and entry-id [@ENTRY_ID@]. cannot retake quiz';
	const ANSWER_ALREADY_EXISTS = "ANSWER_ALREADY_EXISTS;NAME,ID;Answer for question [@PARENT_ID] already exists for [@userEntry]";
}
