<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface EntryDisplayInSearchType extends BaseEnum
{
	const RECYCLED = -2;
	const SYSTEM = -1;
	const NONE = 0;
	const PARTNER_ONLY = 1;
	const KALTURA_NETWORK = 2;
}
