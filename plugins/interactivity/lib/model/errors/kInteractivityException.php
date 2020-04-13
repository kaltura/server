<?php
/**
 * @package plugins.interactivity
 * @subpackage model.errors
 */

class kInteractivityException extends kCoreException
{
	const NEWER_VERSION_DATA_EXISTS = 'newer version data exists';
	const ILLEGAL_FIELD_VALUE = 'illegal field value';
	const ENTRY_ILLEGAL_NODE_NUMBER = 'entry illegal node number';
	const ILLEGAL_ENTRY_NODE_ENTRY_ID = 'illegal entry node entry id';
}