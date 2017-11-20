<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorServiceTurnAroundTime extends BaseEnum
{
	const BEST_EFFORT			= -1;
	const IMMEDIATE				= 0;
	const FIVE_MINUTES			= 300;
	const THIRTHY_MINUTES		= 1800;
	const THREE_HOURS			= 10800;
	const SIX_HOURS				= 21600;
	const TWENTY_FOUR_HOURS		= 86400;
	const FORTY_EIGHT_HOURS		= 172800;
}