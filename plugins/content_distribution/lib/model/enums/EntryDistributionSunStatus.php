<?php
interface EntryDistributionSunStatus extends BaseEnum
{
	const BEFORE_SUNRISE = 1;
	const AFTER_SUNRISE = 2;
	const AFTER_SUNSET = 3;
}