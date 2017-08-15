<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface EntryServerNodeType extends BaseEnum
{
	const LIVE_PRIMARY = 0;
	const LIVE_BACKUP = 1;
	const LIVE_CLIPPING_TASK = 2;
}