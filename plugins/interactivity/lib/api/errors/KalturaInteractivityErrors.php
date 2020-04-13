<?php
/**
 * @package plugins.interactivity
 * @subpackage api.errors
 */
class KalturaInteractivityErrors extends KalturaErrors
{
	const NO_INTERACTIVITY_DATA = "NO_INTERACTIVITY_DATA;entryId;No interactivity data for entry \"@entryId@\"";
	const NO_VOLATILE_INTERACTIVITY_DATA = "NO_VOLATILE_INTERACTIVITY_DATA;entryId;No volatile interactivity data for entry \"@entryId@\"";
	const INTERACTIVITY_DATA_ALREADY_EXISTS = 'INTERACTIVITY_DATA_ALREADY_EXISTS;;There is already an existing interactivity data';
	const VOLATILE_INTERACTIVITY_DATA_ALREADY_EXISTS = 'VOLATILE_INTERACTIVITY_DATA_ALREADY_EXISTS;;There is already an existing volatile interactivity data';
	const DATA_IS_NOT_VALID_JSON = 'DATA_IS_NOT_VALID_JSON;;Data is not valid json';
	const NEWER_VERSION_DATA_EXISTS = 'NEWER_VERSION_DATA_EXISTS;;There is a newer version of the data';
	const ENTRY_ILLEGAL_NODE_NUMBER = 'ENTRY_ILLEGAL_NODE_NUMBER;;Entry interactivity data must have exactly one node';
	const ILLEGAL_ENTRY_NODE_ENTRY_ID = 'ILLEGAL_ENTRY_NODE_ENTRY_ID;;Entry node must have identical entry id to the entry the interaction attached to';
	const VERSION_IS_MANDATORY = 'VERSION_IS_MANDATORY;;Version is mandatory when updating';
	const ILLEGAL_FIELD_VALUE = "ILLEGAL_FIELD_VALUE;errMsg;Illegal field value \"@errMsg@\"";
}