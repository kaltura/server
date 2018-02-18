<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.en
 */
interface QuestionType extends BaseEnum
{
	const MULTIPLE_CHOICE_ANSWER = 1;
	const TRUE_FALSE = 2;
	const REFLECTION_POINT = 3;
	const MULTIPLE_ANSWER_QUESTION = 4;
	const FILL_IN_BLANK = 5;
	const HOT_SPOT = 6;
	const GO_TO = 7;
}
