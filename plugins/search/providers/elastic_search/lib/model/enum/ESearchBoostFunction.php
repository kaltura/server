<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
interface ESearchBoostFunction extends BaseEnum
{
	const EXP = 'exp';
}

interface ESearchBoostField extends BaseEnum
{
	const CREATED_AT = 'created_at';
}

interface ESearchBoostMode extends BaseEnum
{
	const MULTIPLY = 'multiply';
}

interface ESearchCreatedAtBoostItems extends BaseEnum
{
	const SCALE_30D = '30d';
	const DECAY_HALF = 0.5;
	const ORIGIN_NOW = 'now';
}