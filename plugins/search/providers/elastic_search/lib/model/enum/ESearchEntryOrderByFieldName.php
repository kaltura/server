<?php
/**
* @package plugins.elasticSearch
* @subpackage model.enum
*/
interface ESearchEntryOrderByFieldName extends BaseEnum
{
    const ENTRY_UPDATED_AT = 'updated_at';
    const ENTRY_CREATED_AT = 'created_at';
    const ENTRY_START_DATE = 'start_date';
    const ENTRY_END_DATE = 'end_date';
}
