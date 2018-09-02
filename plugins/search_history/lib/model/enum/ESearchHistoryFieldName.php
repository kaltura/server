<?php
/**
 * @package plugins.searchHistory
 * @subpackage model.enum
 */
interface ESearchHistoryFieldName extends BaseEnum
{
	const PARTNER_ID = 'partner_id';
	const KUSER_ID = 'kuser_id';
	const TIMESTAMP = 'timestamp';
	const SEARCH_TERM = 'search_term';
	const SEARCHED_OBJECT = 'searched_object';
	const PID_UID_CONTEXT = 'pid_uid_context';
	const SEARCH_CONTEXT = 'search_context';
}
