<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface executionScope extends BaseEnum
{
	const INDEXING = 'indexing';
	const FROM_OBJECT = 'from_object';
	const PLAY_MANIFEST = 'playManifest';
	const NONE = null;
}