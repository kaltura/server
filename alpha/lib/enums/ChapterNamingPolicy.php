<?php
/**
 * @package api
 * @subpackage enum
 */
interface ChapterNamingPolicy extends BaseEnum
{
	const BY_ENTRY_ID = 1;
	const BY_ENTRY_NAME = 2;
	const NUMERICAL = 3;
}