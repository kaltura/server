<?php
/**
 * Created by IntelliJ IDEA.
 * User: roie.beck
 * Date: 1/30/2018
 * Time: 10:33 AM
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
