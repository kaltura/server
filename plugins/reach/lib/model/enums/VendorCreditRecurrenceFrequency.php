<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorCreditRecurrenceFrequency extends BaseEnum
{
	const DAILY = "day";
	const WEEKLY = "week";
	const MONTHLY = "month";
	const YEARLY = "year";
}