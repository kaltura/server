<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.errors
 */
class KalturaESearchErrors extends KalturaErrors
{
    const SEARCH_TYPE_NOT_ALLOWED_ON_FIELD = "SEARCH_TYPE_NOT_ALLOWED_ON_FIELD;TYPE,FIELD; Type of search [@TYPE@] not allowed on specific field [@FIELD@]";
    const EMPTY_SEARCH_TERM_NOT_ALLOWED = "EMPTY SEARCH TERM IS NOT ALLOWED;FIELD,TYPE; Empty search term is not allowed on Field [@FIELD@] and search type [@TYPE@]";
    const SEARCH_TYPE_NOT_ALLOWED_ON_UNIFIED_SEARCH = 'SEARCH TYPE IS NOT ALLOWED ON UNIFIED SEARCH;TYPE; Type of search [@TYPE@] not allowed on unified search';
    const EMPTY_SEARCH_OPERATOR_NOT_ALLOWED = 'EMPTY SEARCH OPERATOR IS NOT ALLOWED;;empty search operator is not allowed';
    const EMPTY_SEARCH_ITEMS_NOT_ALLOWED = 'EMPTY SEARCH ITEMS ARE NOT ALLOWED;;empty search items are not allowed';

    //Query parsing errors
    const UNMATCHING_BRACKETS = 'UNMATCHING BRACKETS;;Unmatching brackets';
    const MISSING_QUERY_OPERAND = 'MISSING QUERY OPERAND;;missing operand [AND / OR / NOT]';
    const UNMATCHING_QUERY_OPERAND = 'UNMATCHING QUERY OPERAND;;unmatching query operand. use same operand between brackets';
    const CONSECUTIVE_OPERANDS_MISMATCH = 'CONSECUTIVE OPERANDS MISMATCH;;Illegal consecutive operands';
    const INVALID_FIELD_NAME= 'INVALID_FIELD_NAME;FIELD_NAME;Illegal query field name [@FIELD_NAME@]';
    const INVALID_METADATA_FORMAT = 'INVALID_METADATA_FORMAT;;Invalid metadate format';
    const INVALID_METADATA_FIELD = 'INVALID METADATA FIELD;FIELD_NAME;Illegal metadata field name [@FIELD_NAME@]. allowed only [xpath, metadata_profile_id, term]';
    const INVALID_MIXED_SERACH_TYPES = 'INVALID_MIXED_SERACH_TYPES;FIELD_NAME;FIELD_VALUE;Illegal mixed search item types. [@FIELD_NAME@] [@FIELD_VALUE@] can\'t be set to starts-with-search / partial-search and range-search simultaneously';



}