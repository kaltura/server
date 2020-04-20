<?php
/**
 * @package plugins.interactivity
 * @subpackage model.errors
 */

class kInteractivityException extends kCoreException
{
	const DIFFERENT_DATA_VERSION = 'different data version';
	const ILLEGAL_FIELD_VALUE = 'illegal field value';
	const ENTRY_ILLEGAL_NODE_NUMBER = 'entry illegal node number';
	const ILLEGAL_ENTRY_NODE_ENTRY_ID = 'illegal entry node entry id';
	const CANT_UPDATE_NO_DATA = 'CANT_UPDATE_NO_DATA';
}