<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
interface ESearchUserOrderByFieldName extends BaseEnum
{
	const UPDATED_AT = 'updated_at';
	const CREATED_AT = 'created_at';
	const SCREEN_NAME = 'screen_name.raw';
	const USER_ID = 'puser_id.raw';
	const FULL_NAME = 'full_name.raw';
}
