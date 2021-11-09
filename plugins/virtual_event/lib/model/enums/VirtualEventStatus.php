<?php
/**
 * @package plugins.virtualEvent
 * @subpackage model.enum
 */
interface VirtualEventStatus extends BaseEnum
{
	/**
	 * Active status
	 */
	const ACTIVE = 2;
	
	/**
	 * Deleted status
	 */
	const DELETED = 3;
}