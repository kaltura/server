<?php
/**
 * @package plugins.quiz
 * @subpackage api.errors
 */

class KalturaQuizErrors extends KalturaErrors
{
	const PROVIDED_ENTRY_IS_ALREADY_A_QUIZ = "PROVIDED_ENTRY_IS_ALREADY_A_QUIZ, provided entry is already a quiz [@ENTRY_ID@]";
	const PROVIDED_ENTRY_IS_NOT_A_QUIZ = "PROVIDED_ENTRY_IS_NOT_A_QUIZ, provided entry is not a quiz [@ENTRY_ID@]";
}