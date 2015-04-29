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
}