<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface reportInterval extends BaseEnum
{
	const MONTHS = 'months';
	const DAYS = 'days';
	const HOURS = 'hours';
	const MINUTES = 'minutes';
	const TEN_SECONDS = 'ten_seconds';
	const TEN_MINUTES = 'ten_minutes';
}
