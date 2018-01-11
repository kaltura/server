<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
interface ESearchCategoryEntryFieldName extends BaseEnum
{
	const NAME = 'name';
	const ID = 'id';
	const FULL_IDS = 'full_ids';
	const ANCESTOR_ID = 'ancestor_id';
	const ANCESTOR_NAME = 'ancestor_name';
}
