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
	const DATA_IS_NOT_VALID_JSON = 'DATA_IS_NOT_VALID_JSON;;Data is not valid JSON';
	const DIFFERENT_DATA_VERSION = "DIFFERENT_DATA_VERSION;currentVersion;Current data have different version \"@currentVersion@\"";
	const ENTRY_ILLEGAL_NODE_NUMBER = 'ENTRY_ILLEGAL_NODE_NUMBER;;Entry interactivity data must have exactly one node';
	const EMPTY_INTERACTIVITY_DATA = 'EMPTY_INTERACTIVITY_DATA;;Interactivity data must not be empty';
	const ILLEGAL_ENTRY_NODE_ENTRY_ID = 'ILLEGAL_ENTRY_NODE_ENTRY_ID;;Entry node must not have entry id';
	const ILLEGAL_FIELD_VALUE = "ILLEGAL_FIELD_VALUE;errMsg;Illegal field value \"@errMsg@\"";
	const DUPLICATE_INTERACTIONS_IDS = 'DUPLICATE_INTERACTIONS_IDS;;The interaction have duplicate interactions ids';
	const DUPLICATE_NODES_IDS = 'DUPLICATE_NODES_IDS;;The interaction have duplicate nodes ids';
	const UNSUPPORTED_PLAYLIST_TYPE = 'UNSUPPORTED_PLAYLIST_TYPE;;You can add playlist interactivity only to playlists of type PATH';
}

