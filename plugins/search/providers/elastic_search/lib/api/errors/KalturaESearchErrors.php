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
	const OBJECTID_AND_OBJECTIDS_NOT_ALLOWED_SIMULTANEOUSLY = 'OBJECTID AND OBJECTIDS NOT ALLOWED SIMULTANEOUSLY;;objectId and objectIds are not allowed to use simultaneously';
    const MISSING_MANDATORY_PARAMETERS_IN_ORDER_ITEM = 'MISSING MANDATORY PARAMETERS IN ORDER ITEM;;missing mandatory parameters in order item';
    const MIXED_SEARCH_ITEMS_IN_NESTED_OPERATOR_NOT_ALLOWED = 'MIXED SEARCH ITEMS IN NESTED OPERATOR NOT ALLOWED;;mixed search items in nested operator not allowed';
    const MISSING_OPERATOR_TYPE = 'MISSING OPERATOR TYPE;;missing operator type';
    const CRITERIA_EXCEEDED_MAX_MATCHES_ALLOWED = 'CRITERIA EXCEEDED MAX MATCHES ALLOWED;;Unable to generate list. max matches value was reached';

    //Query parsing errors
    const UNMATCHING_BRACKETS = 'UNMATCHING BRACKETS;;Unmatching brackets';
    const MISSING_QUERY_OPERAND = 'MISSING QUERY OPERAND;;missing operand [AND / OR / NOT]';
    const UNMATCHING_QUERY_OPERAND = 'UNMATCHING QUERY OPERAND;;unmatching query operand. use same operand between brackets';
    const CONSECUTIVE_OPERANDS_MISMATCH = 'CONSECUTIVE OPERANDS MISMATCH;;Illegal consecutive operands';
    const INVALID_FIELD_NAME= 'INVALID_FIELD_NAME;FIELD_NAME;Illegal query field name [@FIELD_NAME@]';
    const INVALID_METADATA_FORMAT = 'INVALID_METADATA_FORMAT;;Invalid metadate format';
    const INVALID_METADATA_FIELD = 'INVALID METADATA FIELD;FIELD_NAME;Illegal metadata field name [@FIELD_NAME@]. allowed only [xpath, metadata_profile_id, term]';
    const INVALID_MIXED_SEARCH_TYPES = 'INVALID_MIXED_SEARCH_TYPES ;FIELD_NAME;FIELD_VALUE;Illegal mixed search item types. [@FIELD_NAME@] [@FIELD_VALUE@] can\'t be set to starts-with-search / partial-search and range-search simultaneously';
    const UNABLE_TO_EXECUTE_ENTRY_CAPTION_ADVANCED_FILTER = 'UNABLE_TO_EXECUTE_ENTRY_CAPTION_ADVANCED_FILTER;;Unable to execute entry caption advanced filter';



}