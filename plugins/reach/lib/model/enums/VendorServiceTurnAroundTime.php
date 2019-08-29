<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorServiceTurnAroundTime extends BaseEnum
{
	const BEST_EFFORT           = -1;
	const IMMEDIATE             = 0;
	const THIRTY_MINUTES        = 1800;
	const TWO_HOURS             = 7200;
	const THREE_HOURS           = 10800;
	const SIX_HOURS             = 21600;
	const EIGHT_HOURS           = 28800;
	const TWELVE_HOURS          = 43200;
	const TWENTY_FOUR_HOURS     = 86400;
	const FORTY_EIGHT_HOURS     = 172800;
	const FOUR_DAYS             = 345600;
	const FIVE_DAYS             = 432000;
	const TEN_DAYS              = 864000;
}
