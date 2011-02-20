<?php
/**
 * @package plugins.shortLink
 * @subpackage model.enum
 */
interface ShortLinkStatus extends BaseEnum
{
	const DISABLED = 1;
	const ENABLED = 2;
	const DELETED = 3;
}