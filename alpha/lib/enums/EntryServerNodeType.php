<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface EntryServerNodeType extends BaseEnum
{
	const LIVE_PRIMARY = 1;
	const LIVE_BACKUP = 2;

}