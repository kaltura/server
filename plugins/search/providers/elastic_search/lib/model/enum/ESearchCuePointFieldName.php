<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */ 
interface ESearchCuePointFieldName extends BaseEnum
{
	const CUE_POINT_ID = 'cue_points.cue_point_id';
	const CUE_POINT_NAME = 'cue_points.cue_point_name';
	const CUE_POINT_TEXT = 'cue_points.cue_point_text';
	const CUE_POINT_TAGS = 'cue_points.cue_point_tags';
	const CUE_POINT_START_TIME = 'cue_points.cue_point_start_time';
	const CUE_POINT_END_TIME = 'cue_points.cue_point_end_time';
	const CUE_POINT_SUB_TYPE = 'cue_points.cue_point_sub_type';
	const CUE_POINT_QUESTION = 'cue_points.cue_point_question';
	const CUE_POINT_ANSWERS = 'cue_points.cue_point_answers';
	const CUE_POINT_HINT = 'cue_points.cue_point_hint';
	const CUE_POINT_EXPLANATION = 'cue_points.cue_point_explanation';
}