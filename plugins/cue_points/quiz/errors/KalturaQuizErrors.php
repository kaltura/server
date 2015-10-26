<?php
/**
 * @package plugins.quiz
 * @subpackage api.errors
 */

class KalturaQuizErrors extends KalturaErrors
{
	const PROVIDED_ENTRY_IS_ALREADY_A_QUIZ = "PROVIDED_ENTRY_IS_ALREADY_A_QUIZ;ENTRY_ID; provided entry [@ENTRY_ID@] is already a quiz";
	const PROVIDED_ENTRY_IS_NOT_A_QUIZ = "PROVIDED_ENTRY_IS_NOT_A_QUIZ;ENTRY_ID; provided entry [@ENTRY_ID@] is not a quiz";
	const PARENT_ID_IS_MISSING = "PARENT_ID_IS_MISSING, parent ID is missing";
	const WRONG_PARENT_TYPE = "WRONG_PARENT_TYPE;ENTRY_ID; Parent cue point id [@ENTRY_ID@] has the wrong type";
	const ANSWER_UPDATE_IS_NOT_ALLOWED = "ANSWER_UPDATE_IS_NOT_ALLOWED;ENTRY_ID; Answer update is not allowed for quiz [@ENTRY_ID@]";
	const USER_ENTRY_QUIZ_ALREADY_SUBMITTED = 'USER_ENTRY_QUIZ_ALREADY_SUBMITTED;The user-entry-quiz id is already submitted, answers cannot be added/updated';
	const ENTRY_ID_NOT_GIVEN = 'ENTRY_ID_NOT_GIVEN; No entry id given';
	const NO_SUCH_FILE_TYPE = 'Document cannot be provided. No such file type';
}