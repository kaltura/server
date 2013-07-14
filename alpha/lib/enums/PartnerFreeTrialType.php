<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface PartnerFreeTrialType extends BaseEnum
{
	const NO_LIMIT = 0;
	const LIMIT_BANDWIDTH = 1;
	const LIMIT_DATE = 2;
}
