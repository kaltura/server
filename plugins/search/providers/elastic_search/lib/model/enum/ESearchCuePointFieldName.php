<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */ 
interface ESearchCuePointFieldName extends BaseEnum
{
	const ID = 'cue_points.cue_point_id';
	const NAME = 'cue_points.cue_point_name';
	const TEXT = 'cue_points.cue_point_text';
	const TAGS = 'cue_points.cue_point_tags';
	const START_TIME = 'cue_points.cue_point_start_time';
	const END_TIME = 'cue_points.cue_point_end_time';
	const SUB_TYPE = 'cue_points.cue_point_sub_type';
	const QUESTION = 'cue_points.cue_point_question';
	const ANSWERS = 'cue_points.cue_point_answers';
	const HINT = 'cue_points.cue_point_hint';
	const EXPLANATION = 'cue_points.cue_point_explanation';
	const CUE_POINTS_TYPE_FIELD = "cue_points.cue_point_type";
}