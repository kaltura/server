<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.errors
 */
class KalturaESearchErrors extends KalturaErrors
{
    const SEARCH_TYPE_NOT_ALLOWED_ON_FIELD = "SEARCH_TYPE_NOT_ALLOWED_ON_FIELD;TYPE,FIELD; Type of search [@TYPE@] not allowed on specific field [@FIELD@]";
    const EMPTY_SEARCH_TERM_NOT_ALLOWED = "EMPTY SEARCH TERM IS NOT ALLOWED;FIELD,TYPE; Empty search term is not allowed on Field [@FIELD@] and search type [@TYPE@]";
}