<?php
/**
 * @package plugins.beacon
 * @subpackage api.errors
 */
class KalturaESearchBeaconErrors extends KalturaESearchErrors
{
	const INVALID_PARAMETER_EXTERNAL_QUERY_OBJECT = 'INVALID MANDATORY PARAMETER externalElasticQueryObject;;INVALID MANDATORY PARAMETER externalElasticQueryObject - json format expected';
	const INVALID_QUERY_FIELD_WITHIN_JSON = 'INVALID QUERY FIELD WITHIN JSON;;Invalid "query" field within externalElasticQueryObject json';
	const MISSING_MANDATORY_PARAMETER_RELATED_OBJECT_TYPE = 'MISSING MANDATORY PARAMETER RELATED OBJECT TYPE;;Missing mandatory parameter related object type Equal \ In';
}
