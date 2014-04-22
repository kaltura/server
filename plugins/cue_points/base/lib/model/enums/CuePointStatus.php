<?php
/**
 * @package plugins.cuePoint
 * @subpackage model.enum
 */
interface CuePointStatus extends BaseEnum
{
	const READY = 1;
	const DELETED = 2;
	const HANDLED = 3;
}