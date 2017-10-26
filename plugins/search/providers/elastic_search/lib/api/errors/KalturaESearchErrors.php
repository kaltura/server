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
}