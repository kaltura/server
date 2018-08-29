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
	const SEARCH_TERM_COMPLETION = 'search_term_completion';
	const SEARCHED_OBJECT = 'searched_object';
	const PID_UID_CONTEXT_OBJECT = 'pid_uid_context_object';
	const CONTEXT_CATEGORY = 'context_category';
	const SEARCH_CONTEXT = 'search_context';
}
