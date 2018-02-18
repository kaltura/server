<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorCreditRecurrenceFrequency extends BaseEnum
{
	const DAILY = DatesGenerator::DAILY;
	const WEEKLY = DatesGenerator::WEEKLY;
	const MONTHLY = DatesGenerator::MONTHLY;
	const YEARLY = DatesGenerator::YEARLY;
}