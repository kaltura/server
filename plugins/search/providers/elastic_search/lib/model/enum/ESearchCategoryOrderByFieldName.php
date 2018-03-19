<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
interface ESearchCategoryOrderByFieldName extends BaseEnum
{
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';
    const ENTRIES_COUNT = 'entries_count';
    const MEMBERS_COUNT = 'members_count';
    const NAME = 'name.raw';
}
