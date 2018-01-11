<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.errors
 */
class kESearchException extends kCoreException
{
    const SEARCH_TYPE_NOT_ALLOWED_ON_FIELD = 'Search type not allowed on field';
    const EMPTY_SEARCH_TERM_NOT_ALLOWED = 'Empty search term is not allowed on Field';
    const SEARCH_TYPE_NOT_ALLOWED_ON_UNIFIED_SEARCH = 'Search type is not allowed on unified search';
    const ELASTIC_SEARCH_ENGINE_ERROR = 'Elastic search engine error';
    const MISSING_PARAMS_FOR_DELETE = 'Missing params for delete';
    const EMPTY_SEARCH_ITEMS_NOT_ALLOWED = 'empty search items are not allowed';
    const MISSING_MANDATORY_PARAMETERS_IN_ORDER_ITEM = 'missing mandatory parameters in order item';

    //Query parsing errors
    const MISSING_QUERY_OPERAND = 'Missing operand [AND / OR / NOT]';
    const UNMATCHING_BRACKETS = 'Unmatching brackets';
    const UNMATCHING_QUERY_OPERAND = 'Unmatching query operand. use same operand between brackets';
    const CONSECUTIVE_OPERANDS_MISMATCH = 'Illegal consecutive operands';
    const INVALID_FIELD_NAME= 'Illegal query field name';
    const INVALID_METADATA_FORMAT= 'Invalid metadata format';
    const INVALID_METADATA_FIELD = 'Illegal metadata field name. allowed only [xpath, metadata_profile_id, term]';
    const INVALID_MIXED_SEARCH_TYPES = "Illegal mixed search item types";

}