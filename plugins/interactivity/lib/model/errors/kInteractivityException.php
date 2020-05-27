<?php
/**
 * @package plugins.interactivity
 * @subpackage model.errors
 */

class kInteractivityException extends kCoreException
{
	const DIFFERENT_DATA_VERSION = 'different data version';
	const ILLEGAL_FIELD_VALUE = 'illegal field value';
	const EMPTY_INTERACTIVITY_DATA = 'empty interactivity data';
	const ENTRY_ILLEGAL_NODE_NUMBER = 'entry illegal node number';
	const ILLEGAL_ENTRY_NODE_ENTRY_ID = 'illegal entry node entry id';
	const CANT_UPDATE_NO_DATA = 'cant_update_no_data';
	const DUPLICATE_NODES_IDS = 'duplicate_nodes_ids';
	const DUPLICATE_INTERACTIONS_IDS = 'duplicate_interactions_ids';
	const UNSUPPORTED_PLAYLIST_TYPE = 'unsupported_playlist_type';
}