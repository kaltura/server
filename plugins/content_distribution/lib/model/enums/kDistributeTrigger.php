<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.enum
 */
interface kDistributeTrigger extends BaseEnum
{
	const ENTRY_READY = 1;
	const MODERATION_APPROVED = 2;
}