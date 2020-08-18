<?php
/**
 * @package plugins.sip
 * @subpackage model.enum
 */
interface SipSourceType extends BaseEnum
{
	const PICTURE_IN_PICTURE = 1;
	const TALKING_HEADS = 2;
	const SCREEN_SHARE = 3;
}