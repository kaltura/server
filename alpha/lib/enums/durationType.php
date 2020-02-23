<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface durationType extends BaseEnum
{
	const ENTRY_DURATION_TYPE_NOTAVAILABLE = "notavailable";
	const ENTRY_DURATION_TYPE_SHORT = "short";
	const ENTRY_DURATION_TYPE_MEDIUM = "medium";
	const ENTRY_DURATION_TYPE_LONG = "long";
}